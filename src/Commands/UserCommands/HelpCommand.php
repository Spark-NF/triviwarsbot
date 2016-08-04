<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/help" command
 */
class HelpCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'help';
    protected $description = 'Show bot commands help';
    protected $usage = '/help or /help <command>';
    protected $version = '1.0.1';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $message_id = $message->getMessageId();
        $command = trim($message->getText(true));

        // Only get enabled commands
        $commands = array_filter($this->telegram->getCommandsList(), function ($command) {
            return (!$command->isSystemCommand() && $command->isEnabled());
        });

        // If no command parameter is passed, show the list
        if ($command === '') {
            // Group commands by ACL
            $admin = [];
            $user = [];
            foreach ($commands as $command) {
                if ($command->isAdminCommand()) {
                    $admin[] = $command;
                } else {
                    $user[] = $command;
                }
            }

            $text = '';
            
            // How commands by role
            if (!empty($admin)) {
                $text .= '*Admin commands*' . "\n";
                foreach ($admin as $command) {
                    $text .= '/' . $command->getName() . ' - ' . $command->getDescription() . "\n";
                }
                $text .= "\n" . '*User commands*' . "\n";
                foreach ($user as $command) {
                    $text .= '/' . $command->getName() . ' - ' . $command->getDescription() . "\n";
                }
            } else {
                // Show all commands
                foreach ($commands as $command) {
                    $text .= '/' . $command->getName() . ' - ' . $command->getDescription() . "\n";
                }
            }

            $text .= "\n" . 'For exact command help type: /help <command>';
        } else {
            $command = str_replace('/', '', $command);
            if (isset($commands[$command])) {
                $command = $commands[$command];
                $text = 'Command: ' . $command->getName() . ' v' . $command->getVersion() . "\n";
                $text .= 'Description: ' . $command->getDescription() . "\n";
                $text .= 'Usage: ' . $command->getUsage();
            } else {
                $text = 'No help available: Command /' . $command . ' not found';
            }
        }

        return Request::sendMessage([
            'chat_id'       => $chat_id,
            'parse_mode'    => 'MARKDOWN',
            'text'          => $text,
        ]);
    }
}
