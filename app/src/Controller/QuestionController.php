<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends AbstractController
{
    /**
     *  @Route("/")
     */
    public function homepage(QuestionRepository $repository)
    {
        $questions = $repository->findAllAskedOrderedByNewest();

        return $this->render('question/homepage.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/question/new")
     */
    public function new(EntityManagerInterface $entityManager)
    {
        $question = new Question();
        $question->setName('Missing pants')
            ->setSlug('missing-pants-'.rand(0, 1000))
            ->setQuestion(<<<EOF
Hi! So... I'm having a *weird* day. Yesterday, I cast a spell
to make my dishes wash themselves. But while I was casting it,
I slipped a little and I think `I also hit my pants with the spell`.
When I woke up this morning, I caught a quick glimpse of my pants
opening the front door and walking out! I've been out all afternoon
(with no pants mind you) searching for them.
Does anyone have a spell to call your pants back?
EOF
            );

            if (rand(1, 10) > 2) {
                $question->setAskedAt(new \DateTimeImmutable(sprintf('-%d days', rand(1, 100))));
            }

            $question->setVotes(rand(-20, 50));

            $entityManager->persist($question);
            $entityManager->flush();
    }

    /**
     * @Route("/questions/{slug}/vote/{id}", name="app_question_vote", methods="POST")
     */
    public function questionVote($slug, Answer $answer, Request $request, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');
        if ($direction === 'up') {
            $answer->upVote();
        } elseif ($direction === 'down') {
            $answer->downVote();
        }

        $entityManager->flush();
        
        return $this->redirectToRoute('app_question', [
            'slug' => $slug
        ]);
    }

     /**
     * @Route("/question/{slug}", name="app_question")
     */
    public function show($slug, EntityManagerInterface $entityManager, Question $question, AnswerRepository $answerRepository): Response
    {
        /*$repository = $entityManager->getRepository(Question::class);
        
        $question = $repository->findOneBy(['slug' => $slug]);
        
        if (!$question) {
            throw $this->createNotFoundException(sprintf('no question found for slug "%s"', $slug));
        }*/
        
        $answers = $question->getAnswers();

        dump($answers);

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }
}