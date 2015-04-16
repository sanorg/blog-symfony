<?php

namespace SpBar\Bundle\BlogBundle\Form\Type\Post;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

use SpBar\Bundle\BlogBundle\Model\ThemeManager;
use SpBar\Bundle\BlogBundle\Model\PostManager;

class EditFormType extends AbstractType
{   
    protected $tm;

    protected $authorizer;

    protected $token;

    public function __construct(ThemeManager $tm, $auth=null, $token=null)
    {
        $this->tm = $tm;
        $this->authorizer = $auth;
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $singleType = $this->tm->getThemesByType('single');
        $generalPost = $this->tm->getThemeBySlug('general_post');

        $builder->add('title', 'text', array(
                        'label' => 'Title of the Post',
                        'attr' => array('placeholder'=>"Title of the Post"),
                        'required'=>true
                    ))
                // ->add('content', 'textarea', array(
                //         'label' => 'Content',
                //         'attr' => array("class"=>"ckeditor"),
                //         'required'=>true
                //     ))
                ->add('content', 'ckeditor', array(
                    'config' => array(
                        'filebrowser_image_browse_url' => array(
                            'route'            => 'elfinder',
                            'route_parameters' => array('instance' => 'ckeditor'),
                        ),
                    ),
                ))
                ->add('postType', 'entity', array(
                        'label' => 'Post Type',
                        'class' => 'SpBarBlogBundle:Theme',
                        'property' => 'name',
                        'choices' => $singleType,                        
                        'preferred_choices' => array($generalPost),
                        'expanded' => true,
                        'multiple' => false,
                        'required' => true
                    ))
                // ->add('image', 'file')
                ->add('featuredImage','elfinder', array('instance'=>'form', 'enable'=>true, 'required'=> false ))
                ->add('category', 'entity', array(
                        'label' => 'Category',
                        'class'=> 'SpBarBlogBundle:Category',
                        'property'=> 'name',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => true
                    ))
                ->add('status', 'choice', array(
                        'label' => 'Publish',
                        'choices' => PostManager::$newPostStatus,
                        'required' => true
                    ))
        ;

        if($this->authorizer->isGranted('ROLE_BLOG_ADMIN'))
        {
            $builder->add('author', 'entity', array(
                    'class' => 'SpBarUserBundle:User',
                    'property' => 'username',
                    'placeholder' => 'Select Author',
                    'required' => true
                ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'spbar_blog_post_edit';
    }
}
