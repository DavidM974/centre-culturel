<?php

namespace App\Validator;


use App\Repository\BookingRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AvailableBookingValidator extends ConstraintValidator
{
    private $bookingRepo;
    public function __construct(BookingRepository $repository)
    {
        $this->bookingRepo = $repository;
    }

    public function validate( $value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\AvailableBooking */

        // verifier qu'il n'y a pas de reservation deja prise

        if (null === $value || '' === $value) {
            return;
        }
        dump($constraint);exit;

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
