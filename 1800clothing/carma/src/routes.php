<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Victor\Models\Costumes;
use Victor\Middleware\Authentication as ApiKeyAuth;
use Victor\Middleware\FileFilter;
// Routes

function resizeImg($file, $maxResolution){

	if(file_exists($file)){
		$originalImage = imagecreatefrompng($file);
		
		$originalWidth = imagesx($originalImage);
		$originalHeight = imagesy($originalImage);
		
		$ratio = $maxResolution / $originalWidth;
		$newWidth = $maxResolution;
		$newHeight = $originalHeight * $ratio;
		
		if($newHeight > $maxResolution){
			$ratio = $maxResolution / $originalHeight;
			$newHeight = $maxResolution;
			$newWidth = $originalWidth * $ratio;
		}
		
		if($originalImage){
			$newImage = imagecreatetruecolor($newWidth, $newHeight);
			imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            imagealphablending($originalImage, true);
			imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
			imagepng($newImage, $file, 9);
		}
	}
};


// PUT route -> updates an account in the database.
// API Key protected API
$app->put('/costume/{costumes_id}', function ($request, $response, $args) {
    $_costume = $request->getParsedBody();

    $costume = Costumes::find($args['costumes_id']);

    //$costume->images = $_costume["images"];
    //$costume->thumbnails = $_costume["thumbnails"];
    $costume->year_from = $_costume["year_from"];
    $costume->year_to = $_costume["year_to"];
    $costume->person = $_costume["person"];
    $costume->clothing = $_costume["clothing"];
    $costume->caption = $_costume["caption"];
    $costume->description = $_costume["description"];
    $costume->source = $_costume["source"];

    $costume->save();

    if ($costume->id) {
        $payload = ['costume_id' => $costume->id, 'costume_url' => '/costume/' . $costume->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(400);
    }
});//->add(new ApiKeyAuth);

// PUT route -> get one costume account and removes image on server and in DB.
// API Key protected API
$app->put('/removeimg/{costumes_id}', function ($request, $response, $args) {
    $costume = Costumes::find($args['costumes_id']);
    // Next two lines delete image and thumbnail from the server
    unlink('/home/ubuntu/workspace' . $costume->image);
    unlink('/home/ubuntu/workspace' . $costume->thumbnail);
    // this line deletes/updates entry in the DB
    $costume->image = "NULL";
    $costume->thumbnail = "NULL";
    $costume->save();

    if ($costume->id) {
        $payload = ['costume_id' => $costume->id, 'costume_url' => '/costume/' . $costume->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(400);
    }
});//->add(new ApiKeyAuth); 

// POST route -> get one costume account and updates image on server and in DB.
// API Key protected API
$app->post('/updateimg/{costumes_id}', function ($request, $response, $args) {
    $costume = Costumes::find($args['costumes_id']);
    $_costume = $request->getParsedBody();
    $uploadFilename = '';
    $newFilename ='';

    $files = $request->getUploadedFiles();
    $imgFile = $files['file'];

    if($imgFile->getError() === UPLOAD_ERR_OK){
        // Gets name of the uploaded file
        $uploadFilename = $imgFile->getClientFilename();
        // Moves the uploaded file to the upload directory and assigns it a unique name
        // to avoid overwriting an existing uploaded file.
        $basename = bin2hex(random_bytes(8));
        $uploadFilename = sprintf('%s.%0.10s', $basename, $uploadFilename);
        // Moves the uploaded file to the directory on server
        $imgFile->moveTo("../assets/tempImg/" . $uploadFilename);

        // checks if the image is jpg/jpeg/gif and converts it to png
        if (exif_imagetype('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename) == IMAGETYPE_JPEG) {
            if(substr($uploadFilename, -4) == 'jpeg'){$newFilename = str_replace('.jpeg', '.png', $uploadFilename);
            }else if(substr($uploadFilename, -3) == 'jpg'){$newFilename = str_replace('.jpg', '.png', $uploadFilename);}
            imagepng(imagecreatefromstring(file_get_contents('/home/ubuntu/workspace/carma/assets/tempImg/' 
            . $uploadFilename)), '/home/ubuntu/workspace/carma/assets/images/' . $newFilename);
            unlink('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename);
            $uploadFilename = $newFilename;
        }else if (exif_imagetype('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename) == IMAGETYPE_GIF) {
            $newFilename = str_replace('.gif', '.png', $uploadFilename);
            imagepng(imagecreatefromstring(file_get_contents('/home/ubuntu/workspace/carma/assets/tempImg/' 
            . $uploadFilename)), '/home/ubuntu/workspace/carma/assets/images/' . $newFilename);
            unlink('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename);
            $uploadFilename = $newFilename;
        }else if (exif_imagetype('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename) == IMAGETYPE_PNG) {
            $copy = copy('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename, 
            '/home/ubuntu/workspace/carma/assets/images/' . $uploadFilename);
            unlink('/home/ubuntu/workspace/carma/assets/tempImg/' . $uploadFilename);
        }
    }
    
    // Copy original image to the thumbnail folder
    $copy = copy('/home/ubuntu/workspace/carma/assets/images/' . $uploadFilename, 
    '/home/ubuntu/workspace/carma/assets/thumbnails/' . $uploadFilename);
    
    // resize images to the customer standart
    resizeImg('/home/ubuntu/workspace/carma/assets/images/' . $uploadFilename, '500');
    resizeImg('/home/ubuntu/workspace/carma/assets/thumbnails/' . $uploadFilename, '200');

    // this line deletes/updates entry in the DB
    $costume->image = '/carma/assets/images/' . $uploadFilename;
    $costume->thumbnail = '/carma/assets/thumbnails/' . $uploadFilename;
    $costume->save();

    if ($costume->id) {
        $payload = ['costume_id' => $costume->id, 'costume_url' => '/costume/' . $costume->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(400);
    }
})->add(new FileFilter);//->add(new ApiKeyAuth); 


// POST route -> create a fully new account in the database.
// API Key protected API
$app->post('/costume', function ($request, $response, $args) {
    $_costume = $request->getParsedBody();
   
    // Creates new entry in the DB
    $costume = new Costumes();
    $costume->year_from = $_costume["year_from"];
    $costume->year_to = $_costume["year_to"];
    $costume->person = $_costume["person"];
    $costume->clothing = $_costume["clothing"];
    $costume->caption = $_costume["caption"];
    $costume->description = $_costume["description"];
    $costume->source = $_costume["source"];
    
    // Saves everything in DB
    $costume->save();

    // Generates response based on the DB success or fail of the DB request.
    if ($costume->id) {
        $payload = ['costume_id' => $costume->id, 'costume_url' => '/costume/' . $costume->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(400);
    }
});//->add(new FileFilter);//->add(new ApiKeyAuth);


// DELETE route -> get one costume account back based on the costume id.
// API Key protected API
$app->delete('/costume/{costumes_id}', function ($request, $response, $args) {
    $costume = Costumes::find($args['costumes_id']);
    // Next two lines delete image and thumbnail from the server
    unlink('/home/ubuntu/workspace' . $costume->image);
    unlink('/home/ubuntu/workspace' . $costume->thumbnail);
    // this line deletes entry from the DB
    $costume->delete();

    if ($costume->exists) {
        return $response->withStatus(400);
    } else {
        return $response->withStatus(204)->withJson($payload);
    }
});//->add(new ApiKeyAuth);


// GET route -> get one costume account back based on the costume id.
// Unprotected API
$app->get('/costume/{costumes_id}', function ($request, $response, $args) {
    $messages = Costumes::find($args['costumes_id']);
    return $response->withStatus(200)->withJson($messages);
});//->add(new ApiKeyAuth);

// GET route -> get all costumes from the database. Returns JSON file.
// Unprotected API
$app->get('/costumes', function ($request, $response, $args) {
    $_message = new Costumes();
    $messages = $_message->all();
    return $response->withStatus(200)->withJson($messages);
});

// GET route -> get all costumes from the database by to one search criteria, year from and year to. Returns JSON file.
// Unprotected API
$app->get('/search/{year}', function ($request, $response, $args) {
    $messages = Costumes::where('year_from', '<=', $args['year'])->where('year_to', '>=', $args['year'])->take(100)->get();
    return $response->withStatus(200)->withJson($messages);
    
});

// GET route -> get all costumes from the database by to search criterias, year from and year to. Returns JSON file.
// Unprotected API
$app->get('/yearssearch/{year_from}/{year_to}', function ($request, $response, $args) {
    $messages = Costumes::where('year_from', '>=', $args['year_from'])->where('year_to', '<=', $args['year_to'])->get();
    return $response->withStatus(200)->withJson($messages);
    
});


// GET route -> when main page requested it routes to the main page
// Unprotected API
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.html', $args);
});
//$app->add(new ApiKeyAuth);

