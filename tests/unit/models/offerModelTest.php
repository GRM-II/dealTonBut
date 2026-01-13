<?php

use PHPUnit\Framework\TestCase;

class offerModelTest extends TestCase
{
    public function testOfferModelClassExists(): void
    {
        $this->assertTrue(class_exists('offerModel'), 'La classe offerModel doit exister');
    }

    public function testGetAllOffersMethodExists(): void
    {
        $this->assertTrue(
            method_exists('offerModel', 'getAllOffers'),
            'La méthode getAllOffers doit exister'
        );
    }

    public function testGetOfferByIdMethodExists(): void
    {
        $this->assertTrue(
            method_exists('offerModel', 'getOfferById'),
            'La méthode getOfferById doit exister'
        );
    }

    public function testCreateOfferMethodExists(): void
    {
        $this->assertTrue(
            method_exists('offerModel', 'createOffer'),
            'La méthode createOffer doit exister'
        );
    }

    public function testDeleteOfferMethodExists(): void
    {
        $this->assertTrue(
            method_exists('offerModel', 'deleteOffer'),
            'La méthode deleteOffer doit exister'
        );
    }

    public function testGetOffersByUserIdMethodExists(): void
    {
        $this->assertTrue(
            method_exists('offerModel', 'getOffersByUserId'),
            'La méthode getOffersByUserId doit exister'
        );
    }

    public function testGetAllOffersReturnsArray(): void
    {
        $result = offerModel::getAllOffers();
        $this->assertIsArray($result, 'getAllOffers doit retourner un tableau');
    }
}

