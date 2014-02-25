<?php
$pid = pcntl_fork();

if ($pid == -1) {
    fprintf(STDERR, "pcntl_fork failed\n");
} elseif ($pid) {
    $w = new EvChild($pid, FALSE, function ($w, $revents) {
        $w->stop();

        printf("Process %d exited with status %d\n", $w->rpid, $w->rstatus);
    });

    Ev::run();

    // Protect against Zombies
    pcntl_wait($status);
} else {
    //Forked child
    exit(2);
}