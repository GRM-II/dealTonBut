<?php

final class User_controller
{
    public function defaultAction()
    {
        $O_user =  new User_model();
        View::show('user/Login', array('user' =>  $O_user->donneMessage()));

    }
}
