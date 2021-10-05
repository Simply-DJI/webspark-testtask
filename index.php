<?php

/*
    Необходимо доработать класс рассылки Newsletter, что бы он отправлял письма
    и пуш нотификации для юзеров из UserRepository.

    За отправку имейла мы считаем вывод в консоль строки: "Email {email} has been sent to user {name}"
    За отправку пуш нотификации: "Push notification has been sent to user {name} with device_id {device_id}"

    Так же необходимо реализовать функциональность для валидации имейлов/пушей:
    1) Нельзя отправлять письма юзерам с невалидными имейлами
    2) Нельзя отправлять пуши юзерам с невалидными device_id. Правила валидации можете придумать сами.
    3) Ничего не отправляем юзерам у которых нет имен
    4) На одно и то же мыло/device_id - можно отправить письмо/пуш только один раз

    Для обеспечения возможности масштабирования системы (добавление новых типов отправок и новых валидаторов),
    можно добавлять и использовать новые классы и другие языковые конструкции php в любом количестве.
    Реализация должна соответствовать принципам ООП
*/

/*
 * /*TEST CLASS
 * get "Email {email} has been sent to user {name}"
 * get "Push notification has been sent to user {name} with device_id {device_id}"
 * ----------------
 * // VALIDATE
 * done 1) VALID NAME
 * done 2) VALID DEVICE_ID
 * done 3) NOTHING WITHOUT NAME
 * done 4) SAME EMAIL OR NAME PUSH ONLY ONCE
 * */


class Newsletter
{
    private User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function sendPush():void{
        //validate device id
        if(!preg_match('/[0-9]/', $this->user->getDeviceId())){
            throw new Exception('Invalid device_id');
        }
        if($this->user->getPushSendCount()>1){
            throw new Exception('A lot push requests');
        }
        print(sprintf('Emaul %s has been sent %s', $this->user->getEmail(),$this->user->getName()));
        $this->user->addPushSendCount();
    }
    public function sendEmail():void{
        //validate email
        if (empty($this->user->getEmail()) || filter_var($this->user->getEmail(), FILTER_VALIDATE_EMAIL)){
            throw new Exception('Invalid email' . filter_var($this->user->getEmail(), FILTER_VALIDATE_EMAIL));
        }
        //too much
        if($this->user->getEmailSendCount() > 1){
            throw new Exception('A lot requests');
        }
        print(sprintf('Push notify has been sent to user %s with device_id %s', $this->user->getName(), $this->user->getDeviceId()));
        $this->user->addEmailSendCount();
    }
    public function getUser(): User{
        return $this->user;
    }
}

class User{
    private ?string $deviceId = null;
    private ?string $name = null;
    private ?string $email = null;
    private int $pushSendCount = 0;
    private int $emailSendCount  = 0;
    public function __construct(string $name){
        $this->name = $name;
    }
    //name
    public function getName():string{
        return $this->name;
    }
    //email
    public function getEmail(): ?string{
        return $this->email;
    }
    //deviceId
    public function getDeviceId(): ?string{
        return $this->deviceId;
    }
    //user
    public function setDeviceId(?string $deviceId): User{
        $this->deviceId = $deviceId;
        return $this;
    }
    //integer
    public function getPushSendCount(): int {
        return $this->pushSendCount;
    }
    public function addPushSendCount(): User{
        $this->pushSendCount++;
        return $this;
    }
    public function getEmailSendCount(): int {
        return $this->emailSendCount;
    }
    public function addEmailSendCount(): User{
        $this->emailSendCount++;
        return $this;
    }
}
class UserRepository
{
    public function getUsers(): array
    {
        return [
            [
                'name' => 'Ivan',
                'email' => 'ivan@test.com',
                'device_id' => 'Ks[dqweer4'
            ],
            [
                'name' => 'Peter',
                'email' => 'peter@test.com'
            ],
            [
                'name' => 'Mark',
                'device_id' => 'Ks[dqweer4'
            ],
            [
                'name' => 'Nina',
                'email' => '...'
            ],
            [
                'name' => 'Luke',
                'device_id' => 'vfehlfg43g'
            ],
            [
                'name' => 'Zerg',
                'device_id' => ''
            ],
            [
                'email' => '...',
                'device_id' => ''
            ]
        ];
    }
}

/**
 * Тут релизовать получение объекта(ов) рассылки Newsletter и вызов(ы) метода send()
 * $newsletter = //... TODO
 * $newsletter->send();
 * ...
 */
$user = new User('Simply');
$user ->setEmail('simply@gmail.com');
$newsletter = new newsletter($user);
try{
    $newsletter->sendEmail();
}catch (Exception $e){
    print ($e->getMessage());
}

