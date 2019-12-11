<?php

namespace App\Http\Controllers;

use App\Model\CMS;
use App\Model\FAQQuestion;
use Illuminate\Http\Request;

class CMSController extends Controller {
	public $cms;

	public function __construct() {
		$this->cms = new CMS;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$pages = $this->cms->fetchPages();
		return view('cms.index', compact('pages'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			if (strpos($request->path(), 'edit') !== false) {
				$type = 'edit';
			} else {
				$type = 'view';
			}

			$page = CMS::where('slug', $id)->first();
			$faqs = FAQQuestion::where('status', '!=', 'DL')->get();
			if (isset($page->id)) {
				return view('cms.view', compact('page', 'type', 'faqs'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cmsPages');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cmsPages');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id = null) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$cms = CMS::where('slug', $id)->first();

			if (isset($cms->id)) {
				$validate = Validator($request->all(), [
					'content' => 'required',
				]);

				if ($validate->fails()) {
					echo json_encode(["status" => 0, 'message' => 'Content cannot be empty.']);
				} else {
					$cms->content = $request->post('content');
					if ($cms->save()) {
						echo json_encode(["status" => 1, 'message' => $cms->name . ' updated successfully.']);
					} else {
						echo json_encode(["status" => 0, 'message' => 'Some error occurred while updating ' . $cms->name . ' content.']);
					}

				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}

		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

	/**
	 * Remove the specified FQA from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$faq = FAQQuestion::find($id);

			if (isset($faq->id)) {
				$faq->status = 'DL';
				if ($faq->save()) {
					echo json_encode(["status" => 1, 'message' => 'FAQ deleted successfully.']);
				} else {
					echo json_encode(["status" => 0, 'message' => 'Some error occurred while deleting the FAQ']);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}

		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

	/**
	 * add FAQs
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function addFAQs(Request $request) {
		$validate = Validator($request->all(), [
			'answers.*' => 'required',
			'questions.*' => 'required',
		]);

		if ($validate->fails()) {
			echo json_encode(["status" => 0, 'message' => 'Question and Answers cannot be empty.']);
		} else {
			$questions = $request->questions;
			$answers = $request->answers;
			$savecount = 0;
			for ($i = 0; $i < count($questions); $i++) {
				$faq = new FAQQuestion;
				$faq->question = $questions[$i];
				$faq->answer = $answers[$i];
				$faq->created_at = date('Y-m-d H:i:s');
				if ($faq->save()) {
					$savecount++;
				}

			}

			if ($savecount == count($questions)) {
				echo json_encode(["status" => 1, 'message' => 'FAQs added successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Unable to save all the FAQs. Kindly review the same.']);
			}

		}
	}

	/**
	 * update FAQs
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function updateFAQ(Request $request) {
		$validate = Validator($request->all(), [
			'answers.*' => 'required',
			'questions.*' => 'required',
		]);

		if ($validate->fails()) {
			echo json_encode(["status" => 0, 'message' => 'Question and Answers cannot be empty.']);
		} else {
			$questions = $request->questions;
			$answers = $request->answers;
			$ids = $request->ids;
			$savecount = 0;
			for ($i = 0; $i < count($questions); $i++) {
				$faq = FAQQuestion::find($ids[$i]);
				$faq->question = $questions[$i];
				$faq->answer = $answers[$i];
				if ($faq->save()) {
					$savecount++;
				}
			}

			if ($savecount == count($questions)) {
				echo json_encode(["status" => 1, 'message' => 'FAQs updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Unable to update all the FAQs. Kindly review the same.']);
			}

		}
	}
}
