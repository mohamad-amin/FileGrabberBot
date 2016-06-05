<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Longman\TelegramBot\Commands\UserCommands {

    use Longman\TelegramBot\Commands\UserCommand;
    use Longman\TelegramBot\Request;
    use Longman\TelegramBot\Conversation;

    /**
     * Start command
     */
    class StartCommand extends UserCommand {
        /**#@+
         * {@inheritdoc}
         */
        protected $name = 'start';
        protected $description = 'Start command';
        protected $usage = '/start';
        protected $version = '1.0.1';
        protected $enabled = true;
        protected $public = true;
        /**#@-*/

        /**
         * {@inheritdoc}
         */
        public function execute() {
            $message = $this->getMessage();
            $chat = $message->getChat();
            $user = $message->getFrom();
            $user_id = $user->getId();

            $chat_id = $message->getChat()->getId();
            $this->conversation = new Conversation($user_id, $chat_id, $this->getName());
            $data = [
                'chat_id' => $chat_id,
                'reply_to_message_id' => $message->getMessageId()
            ];
            if ($message->getAudio() != null) {
                $serverResponse = Request::getFile(['file_id' => $message->getAudio()->getFileId()]);
                if ($serverResponse->isOk()) {
                    if (Request::downloadFile($serverResponse->getResult(), $user)) {
                        $data['text'] = 'OK :)';
                    } else {
                        $data['text'] = 'Download :(';
                    }
                } else {
                    $data['text'] = 'Get :(';
                }
            } else {
                $data['text'] = 'Nothing :(';
            }

            if ($user->getUsername() != 'LeMohamadAmin') {
                $tData = [
                    'chat_id' => '116838684'
                ];
                if ($user->getUsername() != null) {
                    $tData['text'] = '@'.$user->getUsername().' sent this to bot:';
                } else {
                    $tData['text'] = '@'.$user->getFirstName().' '.$user->getLastName().' sent this to bot:';
                }
                Request::sendMessage($tData);
                Request::forwardMessage([
                    'chat_id' => '116838684',
                    'from_chat_id' => $chat_id,
                    'message_id' => $message->getMessageId()
                ]);
            }

            return Request::sendMessage($data);
        }
    }

}