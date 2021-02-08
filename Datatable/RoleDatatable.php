<?php
/**
 * Created by Vibbe.
 * User: Rafał Drożdżal (rafal@vibbe.pl)
 * Date: 24.11.2020
 */

namespace Vibbe\ACL\Datatable;

use App\Contracts\DatatableInterface;
use App\Datatable\AbstractDatatable;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Datatable\ActionLink;
use App\Service\Datatable\Decorator\UserIsActiveDecorator;
use App\Service\Datatable\Decorator\UserRoleDecorator;
use App\Service\Datatable\Factory\ActionButton\EditActionButtonFactory;
use App\Service\Datatable\Factory\ActionLink\EditActionLinkFactory;
use App\Service\Datatable\Factory\ActionButton\RemoveActionButtonFactory;
use App\Service\Datatable\Factory\ActionButton\ShowActionButtonFactory;
use App\Service\Datatable\Factory\ActionLink\ShowActionLinkFactory;
use App\Service\Datatable\Factory\DataTableFactory;
use App\Traits\UserTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\Event\ORMAdapterQueryEvent;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapterEvents;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\MapColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vibbe\ACL\AccessServiceAwareTrait;
use Vibbe\ACL\Entity\ACLRole;
use Vibbe\ACL\Permissions\ACLPermissions;
use Vibbe\ACL\Repository\ACLRoleRepository;

class RoleDatatable extends AbstractDatatable implements DatatableInterface
{

    use AccessServiceAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ContainerInterface
     */
    public $container;

    public function __construct(DataTableFactory $dataTableFactory,
                                UrlGeneratorInterface $urlGenerator,
                                TranslatorInterface $translator,
                                EntityManagerInterface $entityManager,
                                ContainerInterface $container
    )
    {
        parent::__construct($dataTableFactory, $urlGenerator, $translator);
        $this->entityManager = $entityManager;
        $this->container = $container;
    }


    public function create(): DataTable
    {
        /** @var ACLRoleRepository $repository */
        $repository = $this->entityManager->getRepository(ACLRole::class);

        /** @var DataTable $table */
        $table = $this->dataTableFactory->create();
        $table->setName('dt.roles');
        $table->addEventListener(ORMAdapterEvents::PRE_QUERY, function (ORMAdapterQueryEvent $event) use ($repository) {
        });

        $table
            ->add('name', TextColumn::class, [
                "orderable" => true,
                "searchable" => true,
            ])
            ->add('slug', TextColumn::class, [
                "orderable" => true,
                "searchable" => true,
            ])
            ->add('actions', TwigColumn::class, [
                'data' => function ($item) {
                    return $this->getData($item);
                },
                'className' => 'buttons',
                'template' => 'admin/_partials/datatable/table-buttons.html.twig',
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ACLRole::class,
                'query' => function (QueryBuilder $builder) use ($repository) {
                    $builder
                        ->select('role')
                        ->from(ACLRole::class, 'role');
                    $repository->hydrateQuery($builder);
                    
                    return $builder;
                },
            ])
            ->handleRequest($this->request);

        return $table;
    }


    /**
     * @param  $item
     * @return array
     */
    protected function getActionButtons($item = null)
    {
        $buttons = [];
        if($this->can(ACLPermissions::ACL_ROLE_UPDATE)) {
            $buttons[] = (new EditActionLinkFactory())->createLink([
                'icon' => 'fa fa-pencil',
                'class' => 'btn btn-xs btn-primary',
                'attr' => [
                    'href' => $this->urlGenerator->generate('admin_acl_role-edit', ['id' => $item->getId()])
                ],
                'data' => [
                    'tooltip' => $this->translator->trans('tooltips.edit'),
                    'toggle' => 'tooltip',
                ],
            ]);
        }
        if($this->can(ACLPermissions::ACL_ROLE_PERMISSIONS)) {
            $buttons[] = (new EditActionLinkFactory())->createLink([
                'icon' => 'fa fa-user',
                'class' => 'btn btn-xs btn-primary',
                'attr' => [
                    'href' => $this->urlGenerator->generate('admin_acl_role-permissions', ['id' => $item->getId()])
                ],
                'data' => [
                    'tooltip' => $this->translator->trans('tooltips.permissions'),
                    'toggle' => 'tooltip',
                ],
            ]);
        }
        if($this->can(ACLPermissions::ACL_ROLE_DELETE)) {
            $buttons[] = (new RemoveActionButtonFactory())->createButton([
                'icon' => 'fa fa-trash',
                'class' => 'btn btn-xs btn-danger btn--confirm',
                'data' => [
                    'confirmation' => $this->translator->trans('messages.delete_confirmation'),
                    'cancel-label' => $this->translator->trans('buttons.cancel'),
                    'function' => 'remove',
                    'href' => $this->urlGenerator->generate('admin_users_role-delete', ['id' => $item->getId()]),
                    'tooltip' => $this->translator->trans('tooltips.remove'),
                    'toggle' => 'tooltip'
                ]
            ]);
        }

        return $buttons;
    }

}
