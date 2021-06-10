<?php
namespace App\Services\Questions\Questions;

use App\Helpers\General\ImageHelper;
use App\Models\Questions\Question;
use App\Services\Questions\Questions\StoreQuestionLinks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class StoreQuestion
{
    private $storeQuestionLinks;

    public function __construct(StoreQuestionLinks $storeQuestionLinks)
    {
        $this->storeQuestionLinks = $storeQuestionLinks;
    }

    /**
     * Save question type here
     * @param  array $params [description]
     * @return Obj
     */
    public function execute($params)
    {
    	$lang = $params["locale"];
        
        $correct_answer = $params["correct_answer"] ?? null;

        $data = [
            "question"            => json_encode([$lang => $params["question"]]),
            "type"                => $params["type"],
            "status"              => $params["is_active"] == 1 ? "active" : "Inactive",
            "evaluation_method"   => $params["evaluation_method"],
            "terms"               => "",
            "image"               => "",
         ];

        $questionSave =  Question::create($data);

        $request = request();
        if ($questionSave && $request->hasFile('file')) {

            $file = $request->file('file');

            $this->uploadImage($questionSave, $file);
        }
        $prepQuestionLink = [];
        
        // saving to question links
        if (isset($questionSave)) {
            if (!empty($params["choices"])) {
                $explodedChoices = explode(",", $params["choices"]);
                $correctAnswers = explode(",", $correct_answer);
                if (count($explodedChoices) > 0) {
                    foreach ($explodedChoices as $val) {
                        $prepQuestionLink[] = [
                            "question" => $questionSave->id,
                            "choice" => (int)$val,
                            "succeeding" => json_encode([]),
                            "iscorrect" => in_array($val, $correctAnswers) ? 1 : 0,
                        ];
                    }
                }else{
                    $prepQuestionLink[] = [
                        "question" => $questionSave->id,
                        "choice" => (int)$explodedChoices[0],
                        "succeeding" => json_encode([]),
                        "iscorrect" => in_array($explodedChoices[0], $correctAnswers) ? 1 : 0
                    ];
                }
            }else{
                $prepQuestionLink[] = [
                    "question" => $questionSave->id,
                    "choice" => 0,
                    "succeeding" => json_encode([]),
                    "iscorrect" => 0,
                ];
            }
            // save question links here
            $this->storeQuestionLinks->execute($prepQuestionLink);
        }

        return $questionSave;
    }

    public function uploadImage($question, $file)
    {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $question->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.questions'),
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         $question->image =  $image->imageUrl();
         $question->save();
      }
    }
}