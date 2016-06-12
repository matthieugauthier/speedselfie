<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\PostRepository;
use AppBundle\Entity\QuestionRepository;
use AppBundle\Service\ShuffleService;

class PopService
{
    /** @var PopService $shuffleService */
    protected $shuffleService;
    /** @var PostRepository $postRepository */
    protected $postRepository;
    /** @var QuestionRepository $questionRepository */
    protected $questionRepository;

    public function __construct(ShuffleService $shuffleService, PostRepository $postRepository, QuestionRepository $questionRepository)
    {
        $this->shuffleService = $shuffleService;
        $this->postRepository = $postRepository;
        $this->questionRepository = $questionRepository;
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
     * @param int $type
     * @param User $myUser
     * @param Array $users
     * @return null|Post
     * @throws \Exception
     */
    public function tryToFindPost($type, $myUser, $users)
    {

        try {
            $posts = $this->postRepository->findBy(['type' => $type, 'author' => $myUser->getId()]);

            $p = new Post();
            $p->setAuthor($myUser);
            $p->setUsers($this->shuffleService->getCoupleOfUserByTypeAndPosts($type, $users, $posts));
            $p->setType($type);
            $p->setQuestion($this->getRandomQuestion($type));

            return $p;
        } catch (\Exception $e) {
            if ($e->getCode() === 8000) {

            } else {
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
