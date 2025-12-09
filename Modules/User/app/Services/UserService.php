<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Modules\User\app\Interfaces\Repositories\UserRepositoryInterface;
use Modules\User\Exceptions\UserException;
use Modules\User\Jobs\SendVerificationEmailJob;
use Modules\User\Notifications\ChangeEmailNotification;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository, protected OTPService $otpService){}

    public function list()
    {
        return $this->repository->index();
    }

    public function getById(string $id)
    {
        return $this->repository->findByField('id', $id);
    }

    public function store(array $data): bool
    {
        return (bool) $this->repository->newItem($data);
    }

    public function update(array $data, string $id): bool
    {
        return $this->repository->updateItem($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->repository->remove($id);
    }


    public function updateProfile(string $id, array $data){
        return $this->repository->updateItem($id, $data);
    }

    public function changeEmail(string $id, string $email): void
    {
        $user = $this->repository->findByField('id', $id);
        $url = URL::temporarySignedRoute('api.user.email.verify', now()->addMinutes(30), [
            'email' => $email,
            'user_id' => $user->id,
        ]);

        if(config('app.async_email_sending', false))
        {
            dispatch(new SendVerificationEmailJob($email, $url));
        }else{
            Notification::route('mail', $email)->notify(new ChangeEmailNotification($url));
        }
    }

    public function changeEmailVerify(string $id, string $email){
        return $this->repository->updateItem($id, [
            'email' => $email,
            'email_verified_at' => now(),
        ]);
    }

    public function sendSetPasswordOTP(string $id){
        $user = $this->repository->findByField('id', $id);
        return $this->otpService->send($user["cellphone"]);
    }

    public function setPassword(string $id, string $code, string $password){
        $user = $this->repository->findByField('id', $id);
        $result = $this->otpService->verify($user["cellphone"], $code);
        if(!$result) {
            throw UserException::invalidOTPCode();
        }
        $password = Hash::make($password);
        return $this->repository->updateItem($id, compact('password'));
    }

    public function changePassword(string $id, string $currentPassword, string $password){
        $user = $this->repository->findByField('id', $id);
        if($user->password && !Hash::check($currentPassword, $user->password)){
            throw UserException::invalidCredentials();
        }
        $password = Hash::make($password);
        return $this->repository->updateItem($id, compact('password'));
    }

}
