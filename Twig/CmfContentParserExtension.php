<?php

namespace MandarinMedien\MMCmfContentBundle\Twig;

use FOS\UserBundle\Entity\User;
use MandarinMedien\MMCmfContentBundle\Controller\ContentNodeConfigurationController;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class CmfContentParserExtension extends AbstractExtension
{
    /**
     * @var ContentNodeConfigurationController
     */
    protected $cmfContentParser;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    protected $notGridableContentNodeClasses;


    public function __construct(TokenStorageInterface $tokenStorage, ContentNodeConfigurationController $cmfContentParser = null)
    {
        $this->cmfContentParser = $cmfContentParser;
        $this->tokenStorage = $tokenStorage;

        $this->notGridableContentNodeClasses = $cmfContentParser->getNotGridableClasses();


    }

    /**
     * checks if the current User is ROLE_USER
     *
     * @return bool
     */
    private function checkUser()
    {
        static $enabled;

        if (!isset($enabled)) {

            $token = $this->tokenStorage->getToken();

            if ($token) {

                $user = $token->getUser();

                if ($user instanceof UserInterface) {
                    if ($user->hasRole('ROLE_USER'))
                        $enabled = $token->isAuthenticated();
                }
            } else
                $enabled = false;
        }

        return $enabled;
    }

    /**
     * registers the twig filter
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('cmfParse',
                array($this, 'cmfParse'),
                array(
                    'is_safe' => array('html'),
                    'needs_environment' => true
                )
            ),
        );
    }

    /**
     * @param Environment $twig
     * @param Node $node
     * @param array $options
     *
     * @return string
     */
    public function cmfParse(Environment $twig, Node $node, array $options = array())
    {
        $html = "";

        if ($node instanceof ContentNode) {

            /**
             * @var ContentNode $node
             */
            $template = $this->cmfContentParser->findTemplate($node);

            $refClass = $this->cmfContentParser->getNativeClassnamimg($node);

            $generated_classes = array("ContentNode", $refClass['name']);

            /**
             * parse css classes
             */
            $display_classes = array();
            $class_string = trim($node->getClasses());

            if ($class_string)
                $display_classes = explode(" ", $class_string);

            /*
             * workaround to avoid broken grid-layouts couz of missing xs sizing
             */
            if ( strpos($class_string,'col-xs') === false && !in_array(get_class($node), $this->notGridableContentNodeClasses))
                $generated_classes[] = "col-xs-12";


            $display_classes = array_merge($display_classes, $generated_classes);
            $display_classes = array_unique($display_classes);

            $html = $twig->render($template,
                array_merge_recursive(
                    array(
                        'node' => $node,
                        'node_class' => $refClass['name'],
                        'node_namespace' => $refClass['namespace'],
                        'display_classes' => implode(" ", $display_classes),
                        'generated_classes' => implode(" ", $generated_classes)
                    )
                    , $options
                )
            );
        }

        /**
         * if user is not authenticated, strip all cmf data
         */

        if (!$this->checkUser())
            $html = $this->stripCmfData($html);

        return $html;

    }

    private function stripCmfData($html)
    {
        $pattern = '$data-cmf-([^=]+)="[^"]+"$im';
        $html = preg_replace($pattern, '', $html);

        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mm_cmf_content_parser_twig_extension';
    }
}