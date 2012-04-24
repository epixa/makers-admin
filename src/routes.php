<?php

// List all submissions that are out of sync
$app->get('/', function() use ($app) {
    $submissions = $app->mongo->submissions->find(array('is_synced' => false));
    $app->render('submissions.phtml', array('submissions' => $submissions));
});

// Render a submission for modification
$app->get('/submission/:id', function($id) use ($app) {
    $submission = $app->mongo->submissions->findOne(array('_id' => new MongoId($id)));
    if (!$submission) {
        $app->notFound();
    }
    $submission = (object)$submission;
    $submission->title = isset($submission->title) ? $submission->title : null;
    $submission->link = isset($submission->link) ? $submission->link : null;
    $submission->content = isset($submission->content) ? $submission->content : null;
    $submission->is_approved = isset($submission->is_approved) ? $submission->is_approved : null;

    $app->render('submission.phtml', array('submission' => (object)$submission));
});

// Update a submission
$app->put('/submission/:id', function($id) use ($app) {
    $mongo = $app->mongo;
    $request = $app->request();

    $submission = $mongo->submissions->findOne(array('_id' => new MongoId($id)));
    if (!$submission) {
        $app->notFound();
    }
    $submission = (object)$submission;

    if ($request->put('is_approved') == '1') {
        $submission->is_approved = true;
    }

    $submission->title = $request->put('title');
    $submission->content = $request->put('content');
    $submission->link = $request->put('link');
    $submission->date_updated = time();
    $mongo->submissions->save($submission);
});

// Lists all past synchronizations
$app->get('/syncs', function() use ($app) {
    $syncs = $app->mongo->syncs->find();
    $app->render('syncs.phtml', array('syncs' => $syncs));
});

// Synchronize our moderated data with the jekyll source files
$app->post('/syncs', function() use ($app) {
    $mongo = $app->mongo;

    $submissions = $mongo->submissions->find(array('is_synced' => false));

    $sync = new stdClass();
    $sync->date_created = time();
    $sync->date_completed = null;
    $sync->status = 'started';
    $mongo->syncs->save($sync);

    foreach ($submissions as $submission) {
        // @todo create or update the appropriate file; on error, continue loop
        $submission['is_synced'] = true;
        $mongo->submissions->save($submission);
    }

    $sync->date_completed = time();
    $sync->status = 'completed';
    $mongo->syncs->save($sync);
    $app->redirect('/syncs');
});
