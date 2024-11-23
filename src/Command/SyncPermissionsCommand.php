<?php

namespace App\Command;

use App\Service\PermissionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:sync-permissions',
    description: 'Đồng bộ quyền giữa danh sách định nghĩa và cơ sở dữ liệu.'
)]
class SyncPermissionsCommand extends Command
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();
        $this->permissionService = $permissionService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Bắt đầu đồng bộ quyền...</info>');

        $this->permissionService->syncPermissions();

        $output->writeln('<info>Đồng bộ quyền hoàn tất!</info>');
        return Command::SUCCESS;
    }
}


// php bin/console app:sync-permissions
