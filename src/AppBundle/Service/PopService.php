<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\PostRepository;
use AppBundle\Entity\QuestionRepository;
use AppBundle\Service\ShuffleService;
use Doctrine\ORM\EntityManager;

class PopService
{
    /** @var PopService $shuffleService */
    protected $shuffleService;
    /** @var PostRepository $postRepository */
    protected $postRepository;
    /** @var QuestionRepository $questionRepository */
    protected $questionRepository;
    /** @var EntityManager $em */
    protected $em;

    public function __construct(ShuffleService $shuffleService, PostRepository $postRepository, QuestionRepository $questionRepository, EntityManager $em)
    {
        $this->shuffleService = $shuffleService;
        $this->postRepository = $postRepository;
        $this->questionRepository = $questionRepository;
        $this->em = $em;
    }

    /**
     * @param int $type
     * @param User $myUser
     * @param Array $users
     * @return null|Post
     * @throws \Exception
     */
    public function getPost($type, User $myUser, $users)
    {
        $types = $this->postRepository->findOpenType($type, $myUser);
        if (count($types)) {
            return $types[0];
        }

        return $this->tryToFindPost($type, $myUser, $users);
    }

    /**
     * @param User $myUser
     * @param Array $users
     * @return null|Post
     * @throws \Exception
     */
    public function getPostSpecialOne(User $myUser, $users)
    {
        $o = ['one1' => null, 'one2' => null];

        $types = $this->postRepository->findOpenType(1, $myUser);

        if (count($types) === 2) {
            $o['one1'] = $types[0];
            $o['one2'] = $types[1];
        }
        if (count($types) === 1) {
            $o['one1'] = $types[0];
            $o['one2'] = $this->tryToFindPost(1, $myUser, $users);
        }
        if (count($types) === 0) {
            $o['one1'] = $this->tryToFindPost(1, $myUser, $users);
            $o['one2'] = $this->tryToFindPost(1, $myUser, $users);
        }

        return $o;
    }

    /**
     * @param int $type
     * @param User $myUser
     * @param Array $users
     * @return null|Post
     * @throws \Exception
     */
    public function tryToFindPost($type, User $myUser, $users)
    {

        try {
            $posts = $this->postRepository->findBy(['type' => $type, 'author' => $myUser->getId()]);

            $p = new Post();
            $p->setAuthor($myUser);
            $p->setUsers($this->shuffleService->getCoupleOfUserByTypeAndPosts($type, $users, $posts));
            $p->setType($type);
            $p->setQuestion($this->getRandomQuestion($type));

            $this->em->persist($p);
            $this->em->flush();

            return $p;
        } catch (\Exception $e) {
            if ($e->getCode() !== 8000 && $e->getCode() !== 8001) {
                throw $e;
            }

        }

        return null;
    }

    public function getRandomQuestion($type)
    {
        $questions = $this->getQuestions($type);
        $key = array_rand($questions);

        return $questions[$key]->getQuestion();
    }

    protected function getQuestions($type)
    {
        if ($type === 1) {
            return $this->questionRepository->findBy(['isGroup' => 0]);
        }

        return $this->questionRepository->findBy(['isGroup' => 1]);
    }
}
