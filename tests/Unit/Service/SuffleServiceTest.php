<?php

namespace Tests\Unit\Service;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Service\ShuffleService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SuffleServiceTest extends WebTestCase
{
    /*
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }*/

    public function testShuffleOneEmpty() {
        $u1 = new User();
        $u1->setUsername('PL0001');
        $u2 = new User();
        $u2->setUsername('PL0002');

        $users = [$u1,$u2];

        $posts = [];

        $shuffleService = new ShuffleService();
        $result = $shuffleService->isUsersInPosts($users, $posts);

        $this->assertFalse($result);
    }

    public function testShuffleOne() {
        $u1 = new User();
        $u1->setUsername('PL0001');
        //$u2 = new User();
        //$u2->setGaia('PL0002');

        $users = [$u1];

        $p = new Post();
        $p->setUsers([$u1]);

        $posts = [$p];

        $shuffleService = new ShuffleService();
        $result = $shuffleService->isUsersInPosts($users, $posts);

        $this->assertTrue($result);
    }

    public function testShuffleTwo() {
        $u1 = new User();
        $u1->setUsername('PL0001');
        $u2 = new User();
        $u2->setUsername('PL0002');

        $users = [$u1,$u2];

        $p = new Post();
        $p->setUsers([$u2,$u1]);

        $posts = [$p];

        $shuffleService = new ShuffleService();
        $result = $shuffleService->isUsersInPosts($users, $posts);

        $this->assertTrue($result);
    }

    public function testShuffleThreeVsTwo() {
        $u1 = new User();
        $u1->setUsername('PL0001');
        $u2 = new User();
        $u2->setUsername('PL0002');
        $u3 = new User();
        $u3->setUsername('PL0003');

        $users = [$u1,$u2,$u3];

        $p = new Post();
        $p->setUsers([$u2,$u1]);

        $posts = [$p];

        $shuffleService = new ShuffleService();
        $result = $shuffleService->isUsersInPosts($users, $posts);

        $this->assertFalse($result);
    }

    public function testShuffleGetRand1w1() {
        $u1 = new User();
        $u1->setUsername('PL0001');
        $u2 = new User();
        $u2->setUsername('PL0002');
        $u3 = new User();
        $u3->setUsername('PL0003');

        $users = [$u1,$u2,$u3];

        $p = new Post();
        $p->setUsers([$u1]);

        $posts = [$p];

        $shuffleService = new ShuffleService();

        for($i=0;$i<10;$i++) {
            $result = $shuffleService->getCoupleOfUserByTypeAndPosts(1, $users, $posts);

            $this->assertNotEquals($result[0]->getGaia(), $u1->getGaia());
        }
    }

    public function testShuffleGetRand1w2() {
        $u1 = new User();
        $u1->setUsername('PL0001');
        $u2 = new User();
        $u2->setUsername('PL0002');
        $u3 = new User();
        $u3->setUsername('PL0003');

        $users = [$u1,$u2,$u3];

        $p = new Post();
        $p->setUsers([$u1]);
        $p2 = new Post();
        $p2->setUsers([$u2]);

        $posts = [$p,$p2];

        $shuffleService = new ShuffleService();

        for($i=0;$i<10;$i++) {
            $result = $shuffleService->getCoupleOfUserByTypeAndPosts(1, $users, $posts);

            $this->assertNotEquals($result[0]->getGaia(), $u1->getGaia());
        }
    }
/*
    public function testShuffleGetRand2w1() {
        $u1 = new User();
        $u1->setGaia('PL0001');
        $u2 = new User();
        $u2->setGaia('PL0002');
        $u3 = new User();
        $u3->setGaia('PL0003');

        $users = [$u1,$u2,$u3];

        $p = new Post();
        $p->setUsers([$u1,$u2]);

        $posts = [$p];

        $shuffleService = new ShuffleService();

        for($i=0;$i<10;$i++) {
            $result = $shuffleService->getCoupleOfUserByTypeAndPosts(2, $users, $posts);


            $this->assertTrue($result[0]->getGaia(), $u1->getGaia());
        }
    }*/
}
