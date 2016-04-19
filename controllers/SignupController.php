<?php

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Created by PhpStorm.
 * User: mbrochard
 * Date: 28/03/2016
 * Time: 19:49
 */
class SignupController {

    public function indexAction(Request $req, Application $app) {
        // Formulaire d'inscription
        $form = $app['form.factory']->createBuilder('form')
            ->add('mail', 'text', array(
                'label' => 'Adresse e-mail',
                'constraints' => array(
                    new Assert\NotBlank(array()),
                    new Assert\Email(array()),
                    new Assert\Callback(array(
                        "methods"   =>  array(function ($email, ExecutionContextInterface $context) use ($app) {
                            $qb = $app['db']->createQueryBuilder();
                            $qb
                                ->select('COUNT(c.id) AS mailCount')
                                ->from('Customer', 'c')
                                ->where('c.mail = ?')
                                ->setParameter(0, $email);
                            $mailCount = $qb->execute()->fetch()['mailCount'];
                            if ($mailCount >= 1) {
                                $context->addViolation("Cette adresse e-mail est déjà utilisée");
                            }
                        }),
                    )),
                ),
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Les mots de passe doivent être identique.',
                'first_options' => array(
                    'label' => 'Mot de passe',
                    'constraints' => new Assert\NotBlank(),
                ),
                'second_options' => array(
                    'label' => 'Confirmez',
                    'constraints' => new Assert\NotBlank(),
                ),
            ))
            ->add('lastName', 'text', array(
                'label' => 'Nom',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('firstName', 'text', array(
                'label' => 'Prénom',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5)),
                ),
            ))
            ->add('address', 'text', array(
                'label' => 'Adresse',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5)),
                ),
            ))
            ->add('town', 'text', array(
                'label' => 'Ville',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5)),
                ),
            ))
            ->add('phoneNumber', 'text', array(
                'label' => 'Numéro de tél.',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 8)),
                ),
            ))
            ->add('register', 'submit', array('label' => 'Créer un compte'))
            ->getForm();

        $form->handleRequest($req);

        if ($form->isValid()) {
            $data = $form->getData();
            $password = $app['security.encoder.digest']->encodePassword($data['password'], '');
            $qb = $app['db']->createQueryBuilder();
            $qb
                ->insert('Customer')
                ->values(
                    array(
                        'lastName' => '?',
                        'firstName' => '?',
                        'address' => '?',
                        'mail' => '?',
                        'password' => '?',
                        'phoneNumber' => '?',
                    )
                )
                ->setParameter(0, $data['lastName'])
                ->setParameter(1, $data['firstName'])
                ->setParameter(2, $data['address'])
                ->setParameter(3, $data['mail'])
                ->setParameter(4, $password)
                ->setParameter(5, $data['phoneNumber'])
                ->execute();

            return $app->redirect('/signup/success');
        }

        return $app['twig']->render('signup.twig', array('form' => $form->createView()));
    }

    public function successAction(Request $req, Application $app) {
        return $app['twig']->render('signup_success.twig');
    }

}