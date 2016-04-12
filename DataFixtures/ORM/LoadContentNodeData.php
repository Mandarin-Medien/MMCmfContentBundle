<?php

namespace MandarinMedien\MMCmfContentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MandarinMedien\MMCmfContentBundle\Entity\ContainerContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\Page;
use MandarinMedien\MMCmfContentBundle\Entity\ParagraphContentNode;
use MandarinMedien\MMCmfContentBundle\Entity\RowContentNode;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


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

    private function createLoremPage(ObjectManager $manager)
    {
        $pageLorem = new Page();
        $pageLorem->setName('Lorem');
        $pageLorem->setVisible(true);

        $pageLorem->setMetaAuthor('');
        $pageLorem->setMetaTitle($pageLorem->getName());
        $pageLorem->setMetaRobots('noindex,nofollow');

        $this->setReference('lorem-page',$pageLorem);

        /**
         * container
         */
        $containerFixed = new ContainerContentNode();
        $containerFixed->setName('Container Fixed');
        $containerFixed->setVisible(true);
        $containerFixed->setParent($pageLorem);
        $manager->persist($containerFixed);

        /**
         * row text
         */

        $rowText = new RowContentNode();
        $rowText->setName('Intro Text Paragraphs');
        $rowText->setVisible(true);
        $rowText->setParent($containerFixed);
        $manager->persist($rowText);


        $loremText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
<br>
Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br>
<br>
Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br>
<br>
Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.<br>
<br>
Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.<br>
<br>
At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero voluptua. est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.<br>
<br>
Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus. ';



        /**
         * Text
         */
        $paragraphText = new ParagraphContentNode();
        $paragraphText->setClasses('col-xs-12');
        $paragraphText->setName('Lorem text');
        $paragraphText->setHeadline('Lorem Master Headline');
        $paragraphText->setHeadlineType('h1');
        $paragraphText->setText($loremText);
        $paragraphText->setParent($rowText);
        $paragraphText->setVisible(true);
        $paragraphText->setTemplate('MMCmfContentBundle:cmf:ParagraphContentNode/ParagraphContentNode_upside_down.html.twig');

        $manager->persist($paragraphText);

        $manager->persist($pageLorem);

        $manager->flush();
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

        $this->setReference('main-start-page',$page);


        /**
         * container
         */
        $containerFixed = new ContainerContentNode();
        $containerFixed->setName('Container Fixed');
        $containerFixed->setVisible(true);
        $containerFixed->setParent($page);
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
        $paragraphText->setText('<center>We have a new range of truly mouthwatering gift hampers and baskets in store now. They range from a mini snack basket for £10, perfect for someone’s desk at work, to a beautifully presented luxury wine, cheese, biscuits, chutney and chocolates hamper for £100.</center>');
        $paragraphText->setParent($rowText);
        $paragraphText->setVisible(true);
        $paragraphText->setTemplate('MMCmfContentBundle:cmf:ParagraphContentNode/ParagraphContentNode_upside_down.html.twig');

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
        $paragraph1->setClasses('col-xs-12');
        $paragraph1->setName('Sushi2Go');
        $paragraph1->setHeadline('Sushi2Go');
        $paragraph1->setHeadlineType('h2');
        $paragraph1->setText('Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.');
        $paragraph1->setImageSource('http://image.shutterstock.com/z/stock-vector-vector-sushi-cartoon-character-illustration-268839575.jpg');
        $paragraph1->setParent($row);
        $paragraph1->setVisible(true);
        $manager->persist($paragraph1);

        $paragraph2 = new ParagraphContentNode();

        $paragraph2->setClasses('col-xs-12 col-sm-9');
        $paragraph2->setName('Melone2Chill');
        $paragraph2->setHeadline('Melone2Chill');
        $paragraph2->setHeadlineType('h2');
        $paragraph2->setText('Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.');
        $paragraph2->setImageSource('http://image.shutterstock.com/z/stock-vector-summer-vector-watermelons-cartoon-character-illustration-291183104.jpg');
        $paragraph2->setParent($row);
        $paragraph2->setVisible(true);
        $manager->persist($paragraph2);

        $paragraph3 = new ParagraphContentNode();
        $paragraph3->setClasses('col-xs-12');
        $paragraph3->setName('Bunny2Stay');
        $paragraph3->setHeadline('Bunny2Stay');
        $paragraph3->setHeadlineType('h2');
        $paragraph3->setText('Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.');
        $paragraph3->setImageSource('http://image.shutterstock.com/z/stock-vector--vector-moon-rabbit-cartoon-character-illustration-chinese-text-means-mid-autumn-festival-295520177.jpg');
        $paragraph3->setParent($row);
        $paragraph3->setVisible(true);
        $manager->persist($paragraph3);


        $manager->persist($page);

        $manager->flush();

        $this->createLoremPage($manager);
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
