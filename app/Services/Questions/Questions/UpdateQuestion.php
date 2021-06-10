<?php
namespace App\Services\Questions\Questions;

use App\Helpers\General\ImageHelper;
use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class UpdateQuestion
{
    private $storeQuestionLinks;

    public function __construct(StoreQuestionLinks $storeQuestionLinks)
    {
        $this->storeQuestionLinks = $storeQuestionLinks;
    }

	/**
     * Update question here
     * @param  array $req [description]
     * @return array
     */
    public function execute($req)
    {
        $correct_answer     = $req["correct_answer"] ?? null;
        $idUpdate           = $req["id"];
        $lang               = $req["lang"];
        $question           = $req["question"];
        $type               = $req["type"];
        $status             = $req["is_active"] == 1 ? "active" : "Inactive";

        $evaluation_method  = $req["evaluation_method"];

        $q = Question::findOrFail($idUpdate);

        $questionVal = string_add_json( $lang, ucwords( $question ), string_remove( $lang, $q->question ) );

        $q->question            = $questionVal;
        $q->type                = $type;
        $q->status              = $status;
        $q->evaluation_method   = $evaluation_method;
        $q->terms               = "";
        $q->image               = "";

        if ($q->update()) {

            $request = request();
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $this->uploadImage($q, $file);
            }
            if (!empty($req["choices"])) {
                $explodedChoices = explode(",", $req["choices"]);
                $correctAnswers = explode(",", $correct_answer);
                if (count($explodedChoices) > 0) {
                    foreach ($explodedChoices as $val) {
                        $prepQuestionLink[] = [
                            "question" => $idUpdate,
                            "choice" => (int)$val,
                            "succeeding" => json_encode([]),
                            "iscorrect" => in_array($val, $correctAnswers) ? 1 : 0,
                        ];
                    }
                }else{
                    $prepQuestionLink[] = [
                        "question" => $idUpdate,
                        "choice" => (int)$explodedChoices[0],
                        "succeeding" => json_encode([]),
                        "iscorrect" => in_array($explodedChoices[0], $correctAnswers) ? 1 : 0
                    ];
                }
            }else{
                $prepQuestionLink[] = [
                    "question" => $idUpdate,
                    "choice" => 0,
                    "succeeding" => json_encode([]),
                    "iscorrect" => 0,
                ];
            }
            $this->storeQuestionLinks->execute($prepQuestionLink, $idUpdate);
        }
        return response()->json(['success' => true, "data" => $q]);
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
