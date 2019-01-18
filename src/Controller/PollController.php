<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Form\PollType;
use App\Repository\PollRepository;
use App\Repository\PollVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\PollVote;
use App\Entity\PollOption;
use App\Entity\User;

/**
 * @Route("/poll")
 */
class PollController extends AbstractController
{
    /**
     * @Route("/", name="poll_index", methods={"GET"})
     */
    public function index(PollRepository $pollRepository): Response
    {   $user = $this->getUser();
        $id = $user -> getId();
        return $this->render('poll/index.html.twig', ['polls' => $pollRepository->findBy(['user'=>$id])]);
    }

    /**
     * @Route("/new", name="poll_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $poll = new Poll();
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $poll->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($poll);
            $entityManager->flush();

            return $this->redirectToRoute('poll_index');
        }

        return $this->render('poll/new.html.twig', [
            'poll' => $poll,
            'form' => $form->createView(),
        ]);
    }
    
    

    /**
     * @Route("/{id}", name="poll_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Poll $poll, PollVoteRepository $pollVoteRepository): Response
    {
        $builder = $this->createFormBuilder();
        $choices = [];
        $user = $poll->getUser();
        $mail = $user->getEmail();
        $pollVerifUser=$pollVoteRepository->findBy([ 'user' => $user, 'poll' => $poll]);
    
        

        
        foreach ($poll->getPollOptions() as $option) {
            $choices[$option->getName()] = $option->getId();
        }$entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($user);
        // $entityManager->flush();
        $builder->add('choice', ChoiceType::class, [
            'label' => 'Votre réponse:',
            'choices' => $choices,
            'expanded' => true
        ]);
        $builder->add('ok', SubmitType::class, [
            'label' => 'Voter'
        ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $id_user = $user -> getId();
            $data = $form->getData();
            $option = $data['choice'];

            $poll_id = $poll -> getId();

            
            $vote = new PollVote(); 
            $vote ->setUser($entityManager->getReference(User::class, $id_user));
            $vote ->setPoll($entityManager->getReference(Poll::class, $poll_id));
            $vote ->setPollOption($entityManager->getReference(PollOption::class, $option));

        
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vote);
            $entityManager->flush();
            return $this->redirectToRoute('poll_index');
        }
        return $this->render('poll/show.html.twig', [
            'poll' => $poll,
            'form' => $form->createView(),
            'mail' => $mail,
            'pollVerifUser' => $pollVerifUser
            
        ]);

    }

    /**
     * @Route("/{id}/edit", name="poll_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Poll $poll): Response
    {
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('poll_index', ['id' => $poll->getId()]);
        }
        
        return $this->render('poll/edit.html.twig', [
            'poll' => $poll,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="poll_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Poll $poll): Response
    {
        if ($this->isCsrfTokenValid('delete'.$poll->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($poll);
            $entityManager->flush();
        }

        return $this->redirectToRoute('poll_index');
    }
}
