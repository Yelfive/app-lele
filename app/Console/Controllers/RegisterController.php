<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-29
 */

namespace App\Console\Controllers;

use App\Components\RegisterCaller;
use App\Components\SinopecApiEncapsulate;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Transaction;
use App\Models\UserPool;
use fk\utility\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Psy\Exception\ErrorException;

class RegisterController extends Command
{
    public $name = 'user:register';

    /**
     * @var UserPool
     */
    protected $userFromPool;

    public function getDescription()
    {
        return 'Register user from user pool';
    }

    // TODO: php artisan user:register <uid>
    public function handle()
    {
        $this->getUserFromPool();
        if (!$this->userFromPool) {
            $this->line("No user found from pool");
            return true;
        }

        try {
            set_error_handler(function ($level, $message, $file = '', $line = 0, $context = []) {
                throw new ErrorException($message, 0, $level, $file, $line);
            });
            if ($this->worker()) return true;
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            $this->error("Validation Error:");
            print_r($errors);
            $this->userFromPool->extra = json_encode($errors, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $ae) {
            $this->userFromPool->extra = json_encode([
                'message' => $ae->getMessage(),
                'trace' => $ae->getTrace()
            ], JSON_UNESCAPED_UNICODE);
        }
        restore_error_handler();
        $this->userFromPool->status = UserPool::STATUS_REGISTER_FAILED;
        $this->userFromPool->update();
        $this->error('Register failed. See `user_pool`.`extra` for more information');
    }

    protected function worker()
    {
        /** @var RegisterCaller $caller */
        $caller = App::make(RegisterCaller::class);
        $request = $this->getRequest();

        /** @var \App\Models\User $user */
        $user = $caller->register($request, $this->userFromPool);
        $this->userFromPool->extra = json_encode($caller->result->toArray(), JSON_UNESCAPED_UNICODE);
        if ($caller->result->code == 200) {
            $this->userFromPool->status = UserPool::STATUS_REGISTER_SUCCEEDED;
            $this->userFromPool->update();
            // Update the order, which belongs to this newly created user
            $this->updateOrderRelated($this->userFromPool->order_id, $user->id);

            $count = UserPool::where('mobile', $this->userFromPool->mobile)
                ->where('id', '!=', $this->userFromPool->id)
                ->where('status', UserPool::STATUS_UNHANDLED)
                ->update(['status' => UserPool::STATUS_DISCARDED]);
            $this->info("Register {$this->userFromPool->mobile} successfully. Discard requests with same mobile: $count");
            return true;
        }

        return false;
    }

    protected function getRequest(): Request
    {
        $input = $this->retrieveInput();
        $this->forgeRequest($input);
        return Request::capture(false);
    }

    /**
     * @return UserPool|null
     */
    protected function getUserFromPool()
    {
        if ($this->userFromPool instanceof UserPool) return $this->userFromPool;

        $this->userFromPool = UserPool::where('paid', UserPool::PAID_YES)
            ->where('status', UserPool::STATUS_UNHANDLED)
            ->first();
        return $this->userFromPool;
    }

    protected function retrieveInput(): array
    {
        $pool = $this->userFromPool;
        $input = json_decode($pool->user_input, true);
        $this->pathToUploadedFile($input, ['idcard_front', 'idcard_back']);

        return $input;
    }

    protected function pathToUploadedFile(&$data, $keys)
    {
        foreach ($keys as $k) {
            if (isset($data[$k])) {
                $filename = base_path(ltrim($data[$k], '/'));
                $data[$k] = new UploadedFile(
                    $filename,
                    $k . pathinfo($filename, PATHINFO_EXTENSION),
                    mime_content_type($filename), filesize($filename), UPLOAD_ERR_OK, true
                );
            }
        }
    }

    /**
     * @param array $input
     */
    protected function forgeRequest(array $input)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $input;
    }

    protected function updateOrderRelated($orderID, $uid)
    {
        Order::where('id', $orderID)->update([Order::CREATED_BY => $uid]);
        OrderGoods::where(['order_id' => $orderID])->update([OrderGoods::CREATED_BY => $uid]);
        Transaction::where('order_id', $orderID)->update([Transaction::CREATED_BY => $uid]);
    }

}