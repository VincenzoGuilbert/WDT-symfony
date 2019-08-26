<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Veille;

use App\Form\AddVideoType;
use App\Repository\VideoRepository;
use App\Repository\VeilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class StreamController extends AbstractController
{

	/**
	 * @Route("/", name="home")
	 */
	public function home()
	{
		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		return $this->render('stream/home.html.twig');
	}


	/**
	 * @Route("/symfony", name="symfony")
	 */
	public function symfony(VideoRepository $repo)
	{

		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		//$repo = $this->getDoctrine()->getRepository(Video::class);
		$videos = $repo->findAll();

		return $this->render('stream/symfony.html.twig', [
			'videos' => $videos,
		]);
	}

	/**
	 * @Route("/veille", name="veille")
	 */
	public function veille(VeilleRepository $veilleRepo)
	{

		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		//$repo = $this->getDoctrine()->getRepository(Video::class);
		$veilles = $veilleRepo->findAll();

		return $this->render('stream/veille.html.twig', [
			'veilles' => $veilles,
		]);
	}

	/**
	 * @Route("/veilleCreate", name="veilleCreate")
	 * @Route("/veilleEdit/{id}", name="veilleEdit")
	 */
	public function veilleform(Veille $veille = null, Request $request, ObjectManager $manager)
	{

		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		if(!$veille)
		{
			$veille = new Veille();
		}

		$form = $this->createFormBuilder($veille)
					 ->add('title')
					 ->add('thumbnails')
					 ->add('link')					 
					 ->add('content')
					 ->getForm();
					 
		$form->handleRequest($request);
		
		$link = $veille->getLink();

		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $link, $matches);
		if(isset($matches[1])){
			
			$res = $matches[1];

			$veille->setLink($res);	
		}

		if($form->isSubmitted()  && $form->isValid())
		{
			if(!$veille->getId())
			{
				$veille->setCreatedAt(new \DateTime());
			}
			
			$manager->persist($veille);
			$manager->flush();

			return $this->redirectToRoute('veilleShow', ['id' => $veille->getId()]);
		}


		return $this->render('stream/veilleCreate.html.twig', [
			'formVeille' => $form->createView(),
			'editMode' => $veille->getId() !== null,
			'veille' => $veille
		]);

	}


	/**
	 * @Route("/create", name="create")
	 * @Route("/edit/{id}", name="edit")
	 */
	public function form(Video $video = null, Request $request, ObjectManager $manager)
	{

		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		if(!$video)
		{
			$video = new Video();
		}

		$form = $this->createFormBuilder($video)
					 ->add('title')
					 ->add('content')
					 ->add('thumbnails')
					 ->add('link')
					 ->getForm();

		$form->handleRequest($request);
		
		$link = $video->getLink();

		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $link, $matches);
		if(isset($matches[1])){
			
			$res = $matches[1];

			$video->setLink($res);	
		}
		


		if($form->isSubmitted()  && $form->isValid())
		{
			if(!$video->getId())
			{
				$video->setCreatedAt(new \DateTime());
			}
			
			$manager->persist($video);
			$manager->flush();

			return $this->redirectToRoute('show', ['id' => $video->getId()]);
		}


		return $this->render('stream/create.html.twig', [
			'formVideo' => $form->createView(),
			'editMode' => $video->getId() !== null,
		]);

	}


	/**
	 * @Route("/show/{id}", name="show")
	 */
	public function show(Video $video/*VideoRepository $repo, $id*/)
	{

		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		//$repo = $this->getDoctrine()->getRepository(Video::class);
		//$video = $repo->find($id);

		return $this->render('stream/show.html.twig', [
			'video' => $video,
		]);
	}

	/**
	 * @Route("/veilleShow/{id}", name="veilleShow")
	 */
	public function showVeille(Veille $veille/*VideoRepository $repo, $id*/)
	{
		
		if(!$this->getUser())
		{
			return $this->redirectToRoute('login');
		}

		//$repo = $this->getDoctrine()->getRepository(Video::class);
		//$video = $repo->find($id);

		return $this->render('stream/veilleShow.html.twig', [
			'veille' => $veille,
			'veilleLink' => $veille->getLink()
		]);
	}
	
}


