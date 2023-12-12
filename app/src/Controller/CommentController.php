<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comments/{id}/vote/{direction<up|down>}", name="app_comment", methods="POST")
     */
    public function commentVote($id, $direction): Response
    {
        //todo use id query database

        //use real logic here to save this to the database
        if ($direction == 'up') {   
            $currentVoteCount = rand(7, 100);
        }
        else{
            $currentVoteCount = rand(0, 5);
        }

        return $this->json(['votes' => $currentVoteCount]);
    }
}
