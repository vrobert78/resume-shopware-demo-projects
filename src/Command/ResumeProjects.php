<?php

namespace App\Command;

use App\PlanRecordReport;
use Platformsh\Client\Connection\Connector;
use Platformsh\Client\PlatformClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;
use Exception;

class ResumeProjects extends Command
{
    protected static $defaultName = 'app:resume-projects';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $executionStart = microtime(true);
        // Initialize the client.
        $client = new PlatformClient(new Connector([
         'api_url' => 'https://proxy.shopware.com'
        ]));

        $client->getConnector()->setApiToken(getenv('API_TOKEN'), 'exchange');

        $collection = $client->getSubscriptions(getenv('ORG_ID'));

        foreach ($collection as $subscriptionRecord) {
           $subscriptionData = $subscriptionRecord->getData();

           try {
              $project=$subscriptionRecord->getProject();

              echo date(DATE_ATOM).' ';
              echo $subscriptionData['id'].' ';
              echo $project->id. ' ';
              echo $project->default_branch;
              echo PHP_EOL;

              if (!empty($project->id) && !empty($project->default_branch)) {
                echo shell_exec("platform environment:resume -p $project->id -e $project->default_branch -y --no-wait");
              }

           }
           catch (Exception $e) {

           }
        }

        echo date(DATE_ATOM)." Script completed";
    }


}