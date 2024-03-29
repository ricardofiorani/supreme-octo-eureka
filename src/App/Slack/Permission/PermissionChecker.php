<?php declare(strict_types=1);

namespace App\Slack\Permission;

use App\Slack\Messages\SlackMentionMessage;
use App\Slack\Permission\Exception\ChannelNotAllowedException;
use App\Slack\Permission\Exception\PermissionException;
use App\Slack\Permission\Exception\UserNotAllowedException;

class PermissionChecker
{
    private array $usersAllowed;
    private array $channelsAllowed;
    private array $usersAllowedToDeployToProduction;

    public function __construct()
    {
        $this->usersAllowed = json_decode(getenv('USERS_ALLOWED'), true);
        $this->channelsAllowed = json_decode(getenv('CHANNELS_ALLOWED'), true);
        $this->usersAllowedToDeployToProduction = json_decode(getenv('USERS_ALLOWED_TO_DEPLOY_TO_PRODUCTION'), true);
    }

    /**
     * @throws PermissionException
     */
    public function checkUser(string $user): void
    {
        if (false === in_array($user, $this->usersAllowed, false)) {
            $message = <<<STRING
 Sorry :hear_no_evil:, I'm programmed to do not follow orders from strangers.
>Maybe you can ask someone to add your id `{$user}` on the allowed users list ?
STRING;

            throw new UserNotAllowedException($message);
        }
    }

    /**
     * @throws PermissionException
     */
    public function checkChannel(string $channel): void
    {
        if (false === in_array($channel, $this->channelsAllowed, false)) {
            $message = <<<STRING
 Sorry :speak_no_evil:, I'm not allowed to operate on this channel.
 >Maybe you can ask someone to add this channel id `{$channel}` on the allowed channels list ?
STRING;
            throw new ChannelNotAllowedException($message);
        }
    }

    /**
     * @throws UserNotAllowedException
     */
    public function checkPermissionToDeployToProduction(string $user): void
    {
        if (false === in_array($user, $this->usersAllowedToDeployToProduction, false)) {
            $message = <<<STRING
 Sorry :hear_no_evil:, I do know you, but I'm programmed to do not allow you to deploy to production.
>Maybe you can ask someone to add your id `{$user}` on the list of users allowed to deploy to production ?
STRING;

            throw new UserNotAllowedException($message);
        }
    }
}
