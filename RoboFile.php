<?php

class RoboFile extends \Robo\Tasks
{

  /**
   * Release current repo as the new upstream
   *
   * @return void
  public function pushUpstream(){

    $this->taskExec("mkdir deploy")->run();
    $this->say('clear out contents of deploy folder');
    $this->taskExec("rm -rf deploy/*")->run();



    $this->say('clone both projects');
    $success = $this->taskExecStack()
      ->exec("chmod 755 web/sites/default")
      ->exec("[ -d web/sites/default/themes ] || mkdir web/sites/default/themes")
      ->run()
      ->wasSuccessful();

git clone git@code.osu.edu:umarketing/standard-built.git deploy/build
git clone git@code.osu.edu:umarketing/standard.git deploy/source

echo "cd to new repo, swap gits and composer install"
cd deploy/source
git checkout master
rm -rf .git
mv ../build/.git .git
mv .gitignore-pantheon .gitignore
composer build-assets
cp web/sites/default/default.custom.settings.php web/sites/default/settings.php

echo "remove git subfolders / submodules which pantheon does not like"
( find web -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf
( find vendor -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf

echo "commit and push to release repo"
git add -A
git commit -m "updates merged"
git push

echo "cleanup"
cd ../..

  }
   */

  /**
   * Pulls down the latest theme git repo for development.
   *
   * @return void
   */
  public function setupTheme()
  {

    $success = $this->taskExecStack()
      ->exec("chmod 755 web/sites/default")
      ->exec("[ -d web/sites/default/themes ] || mkdir web/sites/default/themes")
      ->run()
      ->wasSuccessful();

    if ($success) $this->shout("Cloning theme repo (if needed)");
    $success = $success && $this->taskExecStack()
      ->stopOnFail()
      ->dir("web/sites/default/themes")
      ->exec("[ -d standard_patterns ] || git clone git@code.osu.edu:umarketing/standard_patterns.git")
      ->run()
      ->wasSuccessful();

    if ($success) $this->shout("Updating theme repo and building theme");
    $success = $success && $this->taskExecStack()
      ->stopOnFail()
      ->dir("web/sites/default/themes/standard_patterns")
      ->exec("git pull")
      ->exec("composer install")
      ->exec("yarn install")
      ->exec("npx gulp compile")
      ->exec("npx gulp build")
      ->run()
      ->wasSuccessful();

    if ($success) $this->shout("Creating convenience Symlink");
    $success = $success && $this->taskExecStack()
      ->exec("[ -d theme ] || ln -s web/sites/default/themes/standard_patterns theme")
      ->run()
      ->wasSuccessful();



    if($success) {
      $this->shout("SUCCESS");
      $this->say("You have just cloned down the standard_patterns theme
        into 'web/sites/default/themes' where it will take precedence over
        the version installed by composer in 'web/themes'. These versions
        may be more current than that installed by composer since the composer.lock file
        pins its versions at particular commits. It also makes it so that you can
        run 'composer install' without overwriting what you are working on. For
        convenience, a 'theme' symlink has been created at the root of the project
        pointing to 'web/sites/default/themes/standard_patterns'. The theme is
        built but not \"watched\" To watch the theme, run \"cd theme; yarn start\"
      ");

    } else {
      $this->shout("ERROR");

      $this->say("There were errors during the setup theme process. Please review the previous error message for more details.");

    }

  }

  /**
   * Creates a local cache of a pantheon environment's database and rsync'd site files in /tmp/standard.
   * @param string $siteName machine name in pantheon (standard-demo-osu, trustees-osu, etc)
   * @param string $env [live|test|dev]
   */
  public function localCache($siteName, $env)
  {
    $this->taskExec("mkdir -p /tmp/standard/{$siteName}.{$env}")->run();
    $this->say('Rsyncing files from local cache to local site');
    $this->taskExec("terminus rsync {$siteName}.{$env}:files /tmp/standard/{$siteName}.{$env}")->run();
    $this->say('Creating a db backup');
    $this->taskExec("terminus backup:create {$siteName}.{$env} --element=db --keep-for=1")->run();
    $this->say('Fetching latest db backup');
    $this->taskExec("rm -f /tmp/standard/{$siteName}.{$env}.sql.gz")->run();
    $this->taskExec("terminus backup:get {$siteName}.{$env} --element=db --to=/tmp/standard/{$siteName}.{$env}.sql.gz")->run();
  }

  /**
   * Replaces your local dev site's files and database with versions from pantheon or a local cache.
   * @param string $siteName machine name in pantheon (standard-demo-osu, trustees-osu, etc)
   * @param string $env [live|test|dev]
   * @param string $source [pantheon|cache]
   */
  public function localReplace($siteName, $env, $source='pantheon')
  {
    switch ($source) {
      case 'pantheon':
        $this->localCache($siteName, $env);
        $this->localReplace($siteName, $env, 'cache');
        break;
      case 'cache':
        if (file_exists("/tmp/standard/{$siteName}.{$env}/files/") && file_exists("/tmp/standard/{$siteName}.{$env}.sql.gz")) {
          $this->say('Rsyncing files from local cache to local site');
          $this->taskExec("rsync -rtvu --delete /tmp/standard/{$siteName}.{$env}/files/ web/sites/default/files/ > /dev/null")->run();
          $this->say('Restoring database.');
          $this->taskExec("echo \"drop database standard; create database standard;\"  | mysql -uroot standard")->run();
          $this->taskExec("gunzip < /tmp/standard/{$siteName}.{$env}.sql.gz  | mysql -uroot standard")->run();
          $this->drush("cache-rebuild --yes");
        }
        else {
          $this->say('Aborting: Locally cached files or database are missing. ');
        }
        break;
      default:
        $this->say('source must be pantheon or cache');
    }
  }

  public function localInstall($profile='osu_standard') {
    $this->taskExec('chmod 777 web/sites/default')->run();
    $this->taskExec('chmod 777 web/sites/default/settings.php')->run();
    $this->drush('site-install ' . $profile . ' --db-url=mysql://root@127.0.0.1:3306/standard --yes install_configure_form.enable_update_status_module=NULL install_configure_form.enable_update_status_emails=NULL');
    $this->drush('user-login -l http://127.0.0.1:8888 --no-browser');
    $this->drush('runserver');
  }

  /**
   * Starts a local server using drush.
   */
  public function localStart() {
      $return = shell_exec('which symfony');
      if (empty($return)) {
        $this->shout(
          "Project requires HTTPS which drush/php server do not support. \n"
          . "Install symfony cli to get a better server. See readme. \n"
          . "https://symfony.com/download\n\n"
          . "Proceeding with drush/php server.");
        $this->drush('user-login -l https://127.0.0.1:8888 --no-browser');
        $this->drush('runserver');
      }
      else {
        $this->drush('user-login -l https://127.0.0.1:8888 --no-browser');
        system('symfony server:start --port=8888');
      }
  }

  /**
   * Creates a new site on the distribution on pantheon.
   * @param $machineName
   * @param $friendlyName
   */
  public function pantheonCreate($machineName, $friendlyName) {
    if (preg_match('/-osu$/', $machineName)) {
      $this->shout('Creating the website instance.');
      $this->taskExec("terminus -ny site:create {$machineName} \"{$friendlyName}\" standard-distribution2 --org=the-ohio-state-university-university-marketing -vvv
")->run();

      $this->shout('Installing the website.');
      $this->taskExec("terminus -ny connection:set {$machineName}.dev sftp")->run();
      $this->taskExec("terminus drush {$machineName}.dev -- si osu_standard --yes --site-name=\"{$friendlyName}\"")->run();
      $this->taskExec("terminus -ny connection:set {$machineName}.dev git")->run();

      $this->shout('Setting up terminus secrets.');
      $this->pantheonSecretsInitialize($machineName, 'dev');

      $this->shout('Initializing Test.');
      $this->taskExec("terminus -ny env:deploy {$machineName}.test ")->run();
      $this->shout('Initializing Live.');
      $this->taskExec("terminus -ny env:deploy {$machineName}.live ")->run();
    }
    else {
      $this->say("Machine names must end in '-osu'");
    }
  }

  /**
   * Set a secret for Pantheon site.
   * Use flag -s to provide comma seperated list of sites or omit for all.
   *
   * @param  $key
   * @param  $value
   * @param array $opts
   * @return void
   */
  public function pantheonSecretsSet($key, $value, $opts = ['site|s' => null])
  {
    $site = $opts['site'] ?? '@all';
    $sites = $this->getSites($site);
    if(!empty($sites)){
      foreach($sites as $site){
        $this->say("Setting secret for $site");
        $this->taskExec("terminus -y secrets:set {$site}.live $key $value")->run();
      }
    }else{
      echo "Could not set secret \{$key: $value\} for site(s): $site";
    }

  }

  /**
   * Sets the secrets needed for saml authentication
   * @param $machineName
   * @param $env
   */
  public function pantheonSecretsInitialize($machineName, $env) {

    $this->taskExec("terminus secrets:set --clear {$machineName}.{$env} samlauth.authentication.sp_entity_id 'https://intcomm.osu.edu/sp' ")->run();
    $this->taskExec("terminus secrets:set {$machineName}.{$env} samlauth.authentication.sp_x509_certificate 'MIID8TCCAlmgAwIBAgIJAPHHhykoWYoVMA0GCSqGSIb3DQEBCwUAMBkxFzAVBgNVBAMTDkMtQzAyU0c5UDRHOFdQMB4XDTE3MDQxMTE5MjkxOFoXDTI3MDQwOTE5MjkxOFowGTEXMBUGA1UEAxMOQy1DMDJTRzlQNEc4V1AwggGiMA0GCSqGSIb3DQEBAQUAA4IBjwAwggGKAoIBgQDRGBVB/mcs5f+E3CFy2+2BM4iI0k8iYcb+gm8jzIDjWZTX1gvTR3ggN2uzoQPJA2pcK4mzYrL5VhVPk6FsTqy2jwUvLpM/8KE53gPtqphfvjXUWFWkvx1vSqX9ElIWcYHZVgsqOjmGIrjtbKF3wWJrWKhQHzZ6UUT7GO9OfotUu7f10+bquUryuiEmULCB9ijxVT38laDe7rwXYyuWgl5k21+Cm/g0PFQZjtxvqnfeDeqvp/3q7wGru/iH5OpkXY5cv+EvR1Ppc7aQJu8F3gefXhg8TTtzfNhSADTQEia1mMTXWjh6/b3eTh3UFoUueY9qXpVhgbj6mMFwsv0XyTaJk1PrVtJ8aWi/Mnz/HSXCcL9Il8moKzfbJUyJTpJ2Rw34lvD/KOU/kUC/Vgaz31Q5k3YuyW5yKpGpE2QSkK7fnd+55tLj89utHpWfk+jesa6zLOfHxkT95JtVPOhU2XHUuH+PKMZ0kwihwRiSoIZt2OAShCapvcNy5pR4pGheKJUCAwEAAaM8MDowGQYDVR0RBBIwEIIOQy1DMDJTRzlQNEc4V1AwHQYDVR0OBBYEFARqrPKCAAD8x6fAFk4M75VsRaJxMA0GCSqGSIb3DQEBCwUAA4IBgQASFQM+VOQJ4n4fCv+vXJbNqnQCHTSmbvRFz8j6Ph+GTrfvgxA0Ho9pl5L+WOgfiYFpc6QmAjFrsqGLaHhyCn9RlkGg6478/KjjsRC0GbMCiYerokuO59osy5zwuzaxpAHVkhlz8uurTjn1tUGiTNlqINa1StZX00NDoBzzcfGUAb/COB/QZ4c2tR+Lu2A8dmcidq6jMM8+AkbctyHKfLkMmFUvcAZMF0jTYMzERTJMreTmXRVRfp9KeiT8PZXq6AzgcEUW5TbBySdJN+5wh5FqWQrFwBYn+XRr+A6bo5a37G1pprFf+Fp6qF2HYMrywC7f1FmV3TDDpr3dpqn7Ocn6C0gycIMa5FsdrND+Y0vLgjA7x0sovMDowsTOZCzvMKbK21302XXRXE4EIGHLXJn6oOzaRMyHYP+y/iKgkHkXnzK9LBsCIUCDqfZIcEpQq+09Ra3qLeeXY/lGZYZ8RqdyKkG1aXhEatCvPTNLYKvyiEuvFTy5uFcf2FhFOs1fdFY=' ")->run();

    // Set the private key using the demo site as an origin.
    $this->taskExec("terminus secrets:set {$machineName}.{$env} samlauth.authentication.sp_private_key \$(terminus secrets:show standard-demo-osu.live samlauth.authentication.sp_private_key) ")->run();
    // Set the private key using the demo site as an origin.
    $this->taskExec("terminus secrets:set {$machineName}.{$env} swiftmailer.transport.smtp_credentials.swiftmailer.password \$(terminus secrets:show standard-demo-osu.live swiftmailer.transport.smtp_credentials.swiftmailer.password) ")->run();
  }

  /**
   * Returns a list of urls for all sites on the distribution.
   * @param string $env
   */
  public function pantheonSites($env='test') {
    $sites = $this->getSites('@all');
    foreach ($sites as $site) {
      $this->say("https://{$env}-{$site}.pantheonsite.io");
    }
  }

  /**
   * Backup specified sites, one at a time. This can be slow.
   * @param $sites
   */
  public function pantheonBackup($sites) {
    $sites = $this->getSites($sites);
    $this->shout("Backing up live environments" );
    foreach ($sites as $site) {
      $this->taskExec("terminus backup:create {$site}.live --yes")->run();
    }
  }

  /**
   * Checks for upstream updates.
   */
  public function pantheonUpdates($sites) {
    $sites = $this->getSites($sites);
    foreach ($sites as $site) {
      $this->taskExec("terminus upstream:updates:list  {$site}.live ")->run();
    }
  }

  /**
   * Stages a deployment to all sites test environments
   * @param $sites @all or a comma delimited list of sites
   * @param $note note for the deployment.
   * @param $batch number of sites to update concurrently.
   */
  public function pantheonStage($sites, $note, $batch=1)
  {
    $sites = $this->getSites($sites);
    if ((count($sites) > 1) && ($batch > 1)) {
      $chunks = array_chunk($sites, $batch);
      foreach ($chunks as $chunk) {
        $task = $this->taskParallelExec();
        for ($i=0; $i<min($batch, count($chunk)); $i++) {
          $task->process("robo pantheon:stage " . $chunk[$i] . " '$note'");
        }
        $task->printed()->run();
      }
    }
    else {
      foreach ($sites as $site) {
        $this->say("Working on {$site}.dev" );
        $this->say("Clearing upstream cache on {$site}");
        $this->taskExec("terminus site:upstream:clear-cache {$site}")->run();
        $this->taskExec("terminus upstream:updates:apply {$site}.dev")->run();

        $this->say("Working on {$site}.test");
        $this->pantheonUpdate($site, 'test', $note);
      }
    }
  }

  /**
   * Releases pending changes on the following sites.
   * @param $sites @all or a comma delimited list of sites
   * @param $note note for the deployment.
   * @param $batch number of sites to update concurrently.
   */
  public function pantheonRelease($sites, $note, $batch=1) {
    $sites = $this->getSites($sites);
    if ((count($sites) > 1) && ($batch > 1)) {
      $chunks = array_chunk($sites, $batch);
      foreach ($chunks as $chunk) {
        $task = $this->taskParallelExec();
        for ($i=0; $i<min($batch, count($chunk)); $i++) {
          $task->process("robo pantheon:release " . $chunk[$i] . " '$note'");
        }
        $task->printed()->run();
      }
    }
    else {
      foreach ($sites as $site) {
        $this->shout("Working on {$site}.live");
        $this->pantheonUpdate($site, 'live', $note);
      }
    }
  }

  /**
   * Creates a developer, manager, and editor to aid in local development.
   */
  public function localUserCreate() {
    $users = [
      [
        'email' => 'donny@example.com',
        'name' => 'DonnySd',
        'role' => 'site_developer'
      ],
      [
        'email' => 'sammy@example.com',
        'name' => 'SammySm',
        'role' => 'site_manager'
      ],
      [
        'email' => 'connie@example.com',
        'name' => 'ConnieCe',
        'role' => 'content_editor'
      ]
    ];
    foreach ($users as $user) {
      $this->drush("user:create {$user['name']} --mail=\"{$user['email']}\" --password=\"{$user['name']}\"");
      $this->drush("user:role:add {$user['role']} {$user['name']}");
    }
  }

  public function projectEnable($sites, $project) {
    $sites = $this->getSites($sites);
    foreach ($sites as $site) {
      $this->taskExec("terminus drush {$site}.live -- pm:enable {$project} --yes")->run();
    }
  }

  public function projectUninstall($sites, $project) {
    $sites = $this->getSites($sites);
    foreach ($sites as $site) {
      $this->taskExec("terminus drush {$site}.live -- pm:uninstall {$project} --yes")->run();
    }
  }

  public function projectAudit($sites) {
    $sites = $this->getSites($sites);

    // Query the sites
    $projectCounts = [];
    $siteProjects = [];
    foreach ($sites as $site) {
      $result = $this->taskExec("terminus drush {$site}.live -- pm:list --format=json")
        ->printOutput(FALSE)
        ->run();
      if ($result->wasSuccessful()) {
        $json = $result->getMessage();
        $projects = json_decode($json);
      }
      foreach ($projects as $project => $details) {
        if ($details->status == "Enabled") {
          if (!isset($projectCounts[$project])) {
            $projectCounts[$project] = 0;
          }
          $projectCounts[$project]++;
          $siteProjects[$site][] = $project;
        }
      }
    }

    // Establish resultset.
    $projects = array_keys($projectCounts);
    sort($projects);
    $results = [
      'enabled' => [
        'anywhere' => $projects,
        'everywhere' => [],
      ],
      'anomalies' => [

      ]
    ];

    // Process our results into the resultset.
    foreach ($projects as $project) {
      if ($projectCounts[$project] == count($sites)) {
        $results['enabled']['everywhere'][] = $project;
      }

      $consistency = $projectCounts[$project] / count($sites);

      if ($consistency < 1) {
        $details = [
          "consistency" => $consistency,
          "consensus" => $consistency < 0.5 ? 'disabled' : 'enabled',
          "deviations" => []
        ];
        foreach ($sites as $site) {
          if ($details["consensus"] == 'disabled') {
            if (in_array($project, $siteProjects[$site])) {
              $details['deviations'][] = $site;
            }
          }
          else {
            if (!in_array($project, $siteProjects[$site])) {
              $details['deviations'][] = $site;
            }
          }
        }
        $results['anomalies'][$project] = $details;
      }
    }

    $this->say(json_encode($results, JSON_PRETTY_PRINT));
  }

  private function audit() {

  }

  /**
   * Performs all operations necessary to update a test or live environment.
   * @param $site
   * @param $env
   * @param $note
   */
  private function pantheonUpdate($site, $env, $note) {
    // Sync content on test.
    if ($env == 'test') {
      $this->taskExec("terminus env:deploy {$site}.test --sync-content --cc --updatedb --yes --note='{$note}'")->run();
    }
    else {
      $this->taskExec("terminus env:deploy {$site}.{$env} --cc --updatedb --yes --note='{$note}'")->run();
    }
    $this->taskExec("terminus drush {$site}.{$env}  -- cache-rebuild --yes")->run();
    $this->taskExec("terminus drush {$site}.{$env}  -- updb --yes")->run();
    $this->taskExec("terminus drush {$site}.{$env}  -- cache-rebuild --yes")->run();
    $this->taskExec("terminus drush {$site}.{$env}  -- features-import-all --yes")->run();
    // We deliberately import features twice because this is sometimes necessary.
    $this->taskExec("terminus drush {$site}.{$env}  -- features-import-all --yes")->run();
    $this->taskExec("terminus drush {$site}.{$env}  -- cache-rebuild --yes")->run();
  }

  private function shout($text) {
    $this->say("\n\n####################################################################\n{$text}\n####################################################################\n\n");
  }

  private function getSites($sites) {
    if ($sites == '@all') {
      $sites = implode(',', $this->all());
    }

    $this->say("Working on sites {$sites}" );
    $sites = explode(',', $sites);
    return $sites;
  }

  private function all() {
    $names = NULL;
    $result = $this->taskExec("terminus site:list --upstream=de094565-e836-4d1d-a4c6-10033b3d5334 --format=json")
      ->printOutput(FALSE)
      ->run();

    if ($result->wasSuccessful()) {
      $json = $result->getMessage();
      $sites = json_decode($json);
      if ($sites) {
        foreach ($sites as $id => $site) {
          $names[] = $site->name;
        }
      }
    }
    sort($names);
    return $names;
  }

  public function siteUsage() {
    $names = FALSE;
    $result = $this->taskExec("terminus org:site:list c9afc493-7d01-0877-ef6c-cfffc2e60dff --format=json")
      ->printOutput(FALSE)
      ->run();

    $csv = null;
    if ($result->wasSuccessful()) {
      $json = $result->getMessage();
      $sites = json_decode($json);
      if ($sites) {
        foreach ($sites as $id => $site) {
          $usage = $this->getSiteUsage($site->name);
          if (!$csv) {
            $csv = 'Name,Plan,Visits,' . implode(',', array_keys($usage)) . ",Pages," . implode(',', array_keys($usage)) . "\n";
          }

          $visits = [];
          $pages = [];
          foreach ($usage as $month => $stats) {
            $visits[] = $stats['visits'];
            $pages[] = $stats['pages_served'];
          }

          $csv .= "{$site->name},{$site->plan_name},Visits," . implode(',', $visits) . ',Pages,' . implode(',', $pages) . "\n";
        }
      }

      $this->say($csv);

    }
    return $names;
  }

  public function getSiteUsage($site) {
    $usage = null;
    $result = $this->taskExec("terminus env:metrics {$site}.live --period=month --format json")
      ->printOutput(FALSE)
      ->run();
    if ($result->wasSuccessful()) {
      $json = $result->getMessage();
      $data = json_decode($json);
      $usage = [];
      foreach ($data->timeseries as $ts => $stats) {
        $usage[date('M/Y', $ts)] = [
          "visits" => $stats->visits,
          "pages_served" => $stats->pages_served,
        ];
      }
    }
    return $usage;
  }


  private function webroot() {
    return dirname(__FILE__) . '/web';
  }

  /**
   * Run a drush command from the project root on the web folder
   * @param $command "cache-rebuild --yes", "runserver", etc
   */
  public function drush($command) {
    $this->taskExec('./vendor/bin/drush -r ' . $this->webroot() . ' ' . $command)->run();
  }
    /**
   * Create a build artifact and deploy it to standard-built.
   *
   * @return void
   */
  public function localRelease()
  {
    # -------------------------------------------------------------------
    # Script to create a build artifact and deploy it to conference-built.
    # -------------------------------------------------------------------

    $this->say("clear out contents of deploy folder");
    $this->taskExec("mkdir -p deploy")->run();
    $this->taskExec("rm -rf deploy")->run();

    $this->say("clone both projects");
    $this->taskExec("git clone --depth 1 git@code.osu.edu:umarketing/standard-built.git deploy/build")->run();
    $this->taskExec("git clone --depth 1 git@code.osu.edu:umarketing/standard.git deploy/source")->run();

    $this->say("cd to new repo, swap gits and composer install");

    $task = $this->taskExecStack()
      ->stopOnFail()
      ->dir("./deploy/source")
      ->exec("git checkout master")
      ->exec("rm -rf .git")
      ->exec("mv ../build/.git .git")
      ->exec("mv .gitignore-pantheon .gitignore")
      ->exec("composer build-assets")
      // handled in composer build-assets
      // ->exec("npm --prefix web/profiles/custom/osu_standard/themes/standard_patterns ci --no-audit")
      ->exec("npm run build")
      ->exec("cp web/sites/default/default.custom.settings.php web/sites/default/settings.php")
      ->run();


    if($task->wasSuccessful()){
      $this->say("Removing git folders.");

      $task = $this->taskExecStack()
        ->stopOnFail()
        ->dir("./deploy/source")
        ->exec('( find web -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf')
        ->exec('( find vendor -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf')
        ->run();

    }

    if($task->wasSuccessful()){
      $this->say("commit and push to release repo");

      $task = $this->taskExecStack()
        ->stopOnFail()
        ->dir("./deploy/source")
        ->exec("git add -A")
        ->exec("git commit -m 'updates merged'")
        ->exec("git push")
        ->run();

    }

    if($task->wasSuccessful()){
      $this->say("Release completed successfully!");
    } else {
      $this->shout("Error(s) encountered during release process. Please see output above for more details. ");
    }

  }

  public function themeBuild()
  {
    $dir = __DIR__ . "/web/profiles/custom/osu_standard/themes/standard_patterns";
    $this->say('Building theme files in: ' . $dir);
    return $this->taskExecStack()
      ->stopOnFail()
      ->dir($dir)
      ->exec("npm ci")
      ->exec("npm run build")
      ->run();
  }

}
