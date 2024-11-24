<?php

namespace App\Command;

use App\Service\UserService;
use App\Service\PermissionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:setup-initial',
    description: 'Thiết lập người dùng đầu tiên nếu chưa có.'
)]
class SetupInitialCommand extends Command
{
    private UserService $userService;
    private PermissionService $permissionService;

    public function __construct(UserService $userService, PermissionService $permissionService)
    {
        parent::__construct();
        $this->userService = $userService;
        $this->permissionService = $permissionService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Kiểm tra nếu chưa có người dùng nào trong hệ thống
        $users = $this->userService->getAllUsers();
        if (empty($users)) {
            $output->writeln('<comment>Không tìm thấy người dùng nào. Tạo người dùng đầu tiên...</comment>');

            // Hỏi mật khẩu từ console
            $helper = $this->getHelper('question');
            $password = '';
            $confirmPassword = '';

            // Lặp lại cho đến khi hai lần nhập mật khẩu khớp
            do {
                $passwordQuestion = new Question('Nhập mật khẩu cho superadmin: ');
                $passwordQuestion->setHidden(true);
                $password = $helper->ask($input, $output, $passwordQuestion);

                $confirmPasswordQuestion = new Question('Nhập lại mật khẩu để xác nhận: ');
                $confirmPasswordQuestion->setHidden(true);
                $confirmPassword = $helper->ask($input, $output, $confirmPasswordQuestion);

                if ($password !== $confirmPassword) {
                    $output->writeln('<error>Mật khẩu không khớp. Vui lòng nhập lại.</error>');
                }
            } while ($password !== $confirmPassword);

            // Tạo người dùng đầu tiên
            $this->userService->createUser([
                'username' => 'superadmin',
                'email' => 'superadmin@scime.vn',
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'isActive' => true,
            ]);

            $output->writeln('<info>Người dùng superadmin đã được tạo thành công.</info>');
        } else {
            $output->writeln('<info>Hệ thống đã có người dùng. Không cần tạo thêm.</info>');
        }

        // Đồng bộ quyền sau khi tạo người dùng
        $output->writeln('<info>Đồng bộ quyền...</info>');
        $this->permissionService->syncPermissions();
        $output->writeln('<info>Đồng bộ quyền hoàn tất.</info>');

        return Command::SUCCESS;
    }
}
