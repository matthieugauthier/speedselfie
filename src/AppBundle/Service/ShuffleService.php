<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;

class ShuffleService
{
    protected $nbTry = 0;

    public function isUsersInPosts($users, $posts)
    {
        /** @var Post $post */
        foreach ($posts as $post) {
            if (count($users) === count($post->getUsers())) {
                $finded = 0;
                /** @var User $pu */
                foreach ($post->getUsers() as $pu) {
                    /** @var User $u */
                    foreach ($users as $u) {
                        if ($pu->getId() === $u->getId()) {
                            $finded++;
                        }
                    }
                }
                if (count($users) === $finded ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getCoupleOfUserByTypeAndPosts($type, $users, $posts)
    {
        if( count($users) < $type) {
            throw new \Exception('Not enough mates',8000);
        }
        if($this->nbTry > 1000 ) {
            throw new \Exception('Not enough mates',8001);
        }
        $this->nbTry++;

        $shortListedUsers = [];

        $shortListedUsersKeys = array_rand($users,$type);
        if(!is_array($shortListedUsersKeys)) {// Because array_rand not made an array with 1
            $shortListedUsersKeys = [$shortListedUsersKeys];
        }
        foreach($shortListedUsersKeys as $shortListedUsersKey) {
            $shortListedUsers[] = $users[$shortListedUsersKey];
        }

        if($this->isUsersInPosts($shortListedUsers,$posts)) {
            return $this->getCoupleOfUserByTypeAndPosts($type, $users, $posts);
        }

        return $shortListedUsers;
    }
}
