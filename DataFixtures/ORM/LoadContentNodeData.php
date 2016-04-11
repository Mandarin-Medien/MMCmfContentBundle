<?php

namespace MandarinMedien\MMCmfContentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MandarinMedien\MMCmfContentBundle\Entity\ContainerContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\Page;
use MandarinMedien\MMCmfContentBundle\Entity\ParagraphContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\RowContentNode;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\Common\Collections\ArrayCollection;


class LoadContentNodeData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $page = new Page();
        $page->setVisible(true);
        $page->setName('Start');

        $page->setMetaAuthor('');
        $page->setMetaTitle($page->getName());
        $page->setMetaRobots('noindex,nofollow');

        /**
         * container
         */
        $containerFixed = new ContainerContentNode();
        $containerFixed->setName('Container Fixed');
        $containerFixed->setVisible(true);
        $manager->persist($containerFixed);

        /**
         * row text
         */

        $rowText = new RowContentNode();
        $rowText->setName('Intro Text Paragraphs');
        $rowText->setVisible(true);
        $rowText->setParent($containerFixed);
        $manager->persist($rowText);


        /**
         * Text
         */
        $paragraphText = new ParagraphContentNode();
        $paragraphText->setClasses('col-xs-12');
        $paragraphText->setName('Intro text');
        #$paragraphText->setHeadline('');
        #$paragraphText->setHeadlineType('h1');
        $paragraphText->setText('We have a new range of truly mouthwatering gift hampers and baskets in store now. They range from a mini snack basket for £10, perfect for someone’s desk at work, to a beautifully presented luxury wine, cheese, biscuits, chutney and chocolates hamper for £100.');
        $paragraphText->setParent($rowText);
        $paragraphText->setVisible(true);
        $manager->persist($paragraphText);



        /**
         * row
         */
        $row = new RowContentNode();
        $row->setName('Feature Paragraphs');
        $row->setVisible(true);
        $row->setParent($containerFixed);
        $manager->persist($row);

        /**
         * Paragraphs
         */
        $paragraph1 = new ParagraphContentNode();
        $paragraph1->setClasses('col-xs-12 col-md-4');
        $paragraph1->setName('Sushi2Go');
        $paragraph1->setHeadline('Sushi2Go');
        $paragraph1->setHeadlineType('h2');
        $paragraph1->setText('Meisterwerk der Technik');
        $paragraph1->setImageSource('http://image.shutterstock.com/z/stock-vector-vector-sushi-cartoon-character-illustration-268839575.jpg');
        $paragraph1->setParent($row);
        $paragraph1->setVisible(true);
        $manager->persist($paragraph1);

        $paragraph2 = new ParagraphContentNode();

        $paragraph2->setClasses('col-xs-12 col-md-4');
        $paragraph2->setName('Melone2Chill');
        $paragraph2->setHeadline('Melone2Chill');
        $paragraph2->setHeadlineType('h2');
        $paragraph2->setText('Grandiose Illustration!');
        $paragraph2->setImageSource('http://image.shutterstock.com/z/stock-vector-summer-vector-watermelons-cartoon-character-illustration-291183104.jpg');
        $paragraph2->setParent($row);
        $paragraph2->setVisible(true);
        $manager->persist($paragraph2);

        $paragraph3 = new ParagraphContentNode();
        $paragraph3->setClasses('col-xs-12 col-md-4');
        $paragraph3->setName('Bunny2Stay');
        $paragraph3->setHeadline('Bunny2Stay');
        $paragraph3->setHeadlineType('h2');
        $paragraph3->setText('Einfach Toll!');
        $paragraph3->setImageSource('http://image.shutterstock.com/z/stock-vector--vector-moon-rabbit-cartoon-character-illustration-chinese-text-means-mid-autumn-festival-295520177.jpg');
        $paragraph3->setParent($row);
        $paragraph3->setVisible(true);
        $manager->persist($paragraph3);


        $page->addNode($containerFixed);

        $manager->persist($page);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
