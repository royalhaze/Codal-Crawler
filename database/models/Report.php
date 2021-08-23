<?php
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../vendor/autoload.php';

use Carbon\Carbon;
use DiDom\Document;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 16:35
 */
class Report extends Model
{
    private $reportURL;

    protected $table = AppConfig::TABLE_PREFIX.'reports';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function Company()
    {
        $this->belongsTo(Company::class);
    }

    public function ReportData()
    {
        $this->hasMany(ReportData::class);
    }

    public static function store_from_search_result($data)
    {
        $parsed_data = self::parse_data_from_search_result($data);

        $parsed_data = array_merge($parsed_data, self::parse_files_from_search_result($data));

        if (isset($parsed_data['has_attachment']) && $parsed_data['has_attachment']) {
            $attach = $parsed_data['attach'];

            unset($parsed_data['attach']);
        }

        $report_url = $parsed_data['report_url'];

        unset($parsed_data['report_url']);

        $db = Report::create($parsed_data);

        if (!$db instanceof Report) {
            throw new \Exception();
        }

        ReportData::create([
           'report_id' => $db->id,
           'title' => 'ReportUrl',
           'value' => CodalConst::CODAL_BASE_URL.$report_url
        ]);

        if (isset($attach)) {
            foreach ($attach as $file) {
                ReportData::create([
                    'report_id' => $db->id,
                    'title' => 'attachment',
                    'value' => $file
                ]);
            }
        }

        if ($data->SuperVision->UnderSupervision == 1){
            self::store_super_vision_data($data->SuperVision,$db->id);
        }

        return true;
    }

    private static function store_super_vision_data($super_vision,$report_id){

        if ($super_vision->AdditionalInfo != null && $super_vision->AdditionalInfo != ''){
            ReportData::create([
               'report_id' => $report_id,
               'title' => 'SupervisionAdditionalInfo',
               'value' => $super_vision->AdditionalInfo
            ]);
        }

        if (is_array($super_vision->Reasons)){
            foreach ($super_vision->Reasons as $item){
                ReportData::create([
                    'report_id' => $report_id,
                    'title' => 'SupervisionReason',
                    'value' => $item
                ]);
            }
        }
    }

    private static function parse_data_from_search_result($data)
    {
        $company = Company::getBySymbol($data->Symbol);

        return [
            'company_id' => $company->id,
            'symbol_id' => $company->symbol_id,
            'symbol' => $data->Symbol,
            'title' => $data->Title,
            'tracking_no' => (int)$data->TracingNo,
            'letter_code' => $data->LetterCode,
            'has_super_vision' => ($data->SuperVision->UnderSupervision == 1) ? true : false,
            'under_super_vision' => ($data->UnderSupervision == 1) ? true : false,
            'publish_time' => self::parse_publish_date_to_date_time($data->PublishDateTime),
            'publish_time_original' => $data->PublishDateTime,
            'crawl_time' => Carbon::now('Asia/Tehran')->toDateTime(),
            'report_url' => $data->Url
        ];
    }

    private static function parse_files_from_search_result($data)
    {
        $links = [];

        if ($data->HasPdf) {
            $links['pdf_url'] = CodalConst::CODAL_BASE_URL . '/' . $data->PdfUrl;
        }

        if ($data->HasExcel) {
            $links['excel_url'] = $data->ExcelUrl;
        }

        if ($data->HasXbrl) {
            $links['xbrl_url'] = $data->XbrlUrl;
        }

        if ($data->HasAttachment) {
            $links['has_attachment'] = true;
            $links['attach'] = self::parse_attachment_from_url($data->AttachmentUrl);
        }

        return $links;
    }

    private static function parse_attachment_from_url($url)
    {
        $url = CodalConst::CODAL_BASE_URL . $url;

        $client = new Client(['verify' => AppConfig::GUZZLE_VERIFY]);

        $res = $client->get($url);

        if ($res->getStatusCode() != 200){
            throw new \Exception('page get error');
        }

        $page = $res->getBody()->getContents();

        $document = new Document($page, false);

        $tr_list = $document->find('#dgAttachmentList')[0]->children();

        $attach = [];

        foreach ($tr_list as $key => $value) {

            if ($key != 0 && $value->getNode()->nodeName == 'tr') {

                $link = $value->getAttribute('onclick',null);

                if ($link != null && is_string($link)) {

                    $tmp = explode("('", $link)[1];

                    $tmp = explode("')", $tmp)[0];

                    $attach[] = CodalConst::CODAL_BASE_URL . '/' . $tmp;
                }

            }
        }

        return $attach;
    }

    private static function parse_publish_date_to_date_time($jalaliDate)
    {
        $jalaliDate = OtherHelpers::convert_fa_num_to_en($jalaliDate);

        $jalaliDate = OtherHelpers::parse_jalali_datetime_string_to_jalalian($jalaliDate);

        return $jalaliDate->toCarbon()->toDateTime();
    }
}