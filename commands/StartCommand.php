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
                'chat_id' => $chat,
                'reply_to_message_id' => $message->getMessageId()
            ];
            if ($message->getAudio() != null) {
                $serverResponse = Request::getFile(['file_id' => $message->getAudio()->getFileId()]);
                if ($serverResponse->isOk()) {
                    if (Request::downloadFile($serverResponse->getResult())) {
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

            return Request::sendMessage($data);
        }
    }

}