<div class="zenbox primary grid6">
<h3><a href="<?php echo $context->site->getURL();?>"><?php echo $context->site->getTitle();?></a>
<?php
    if (\UNL\VisitorChat\User\Service::getCurrentUser()->managesSite($context->site->getURL()) || \UNL\VisitorChat\User\Service::getCurrentUser()->isAdmin()) {
        echo "<a class='zen-header-link' href='" . \UNL\VisitorChat\Controller::$URLService->generateSiteURL('history/sites/' . $context->site->getURL()) . "'>History</a>";
    }
    ?></h3>

<ul>
    <li>Support Email: <?php echo $context->site->getEmail();?></li>
</ul>

<h4>Members</h4>
<ul>
    <?php 
        foreach ($context->site->getMembers() as $member) {
            if (!$chatUser = \UNL\VisitorChat\User\Record::getByUID($member->getUID())) {
                echo "<li>" . $member->getUID() . " (" . $member->getRole() . ") [this member has not logged into the chat system yet]</li>";
                continue;
            }
            
            $alias = "";
            if (!empty($chatUser->alias)) {
                $alias = " (" . $chatUser->alias . ")";
            }
            
            echo "<li class='" . strtolower($chatUser->status) . "'><a href='" . \UNL\VisitorChat\Controller::$URLService->generateSiteURL('users/' . $chatUser->id) . "'>" . $chatUser->name . "</a> $alias (" . $member->getRole() . ")</li>";
        }
    ?>
</ul>
</div>