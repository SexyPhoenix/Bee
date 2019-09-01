<?php 

namespace App\Services\Bee;

use Illuminate\Support\Facades\Mail;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;

class EmailService
{   
    protected $items;

    protected $users;

    protected $userMap;

    protected $can = [
        TypeConstant::TYPE_HU_LI_JIA => 'App\Mail\Bee\Nursing'
    ];

    public function handle($items)
    {
        $this->setItems($items);

        if(empty($this->items)) return true;

        $this->resetUsers();

        $this->send();
    }

    public function send()
    {
        foreach ($this->items as $uid => $item) {
            
            foreach ($item as $type_id => $m) {
                
                if(TypeConstant::TYPE_HU_LI_JIA == $type_id 
                    && $this->userMap[$uid]->region >= 600) continue;

                Mail::to($this->userMap[$uid])->send(new $this->can[$type_id]($this->userMap[$uid]));
                
            }
        }
    }

    public function sendEmail()
    {

    }

    public function resetUsers()
    {
        foreach (User::getUserById($this->users) as $user) {
            
            $this->userMap[$user->user_id] = $user;
        }
    }

    public function setItems($items)
    {
        foreach ($items as $item) {
            
            $type_id = $item['type_id'];
            $user_id = $item['user_id'];

            if($this->isCan($type_id)) {

                if(!isset($this->items[$user_id][$type_id])) {

                    $this->users[] = $user_id;
                    $this->items[$user_id][$type_id] = true;
                }
            }
        }
    }

    public function isCan($type_id)
    {
        return  array_key_exists($type_id, $this->can);
    }

}