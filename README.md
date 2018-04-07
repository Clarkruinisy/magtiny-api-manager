# magtiny-api-server

Author: Clark Yanwei Zhao <zhaoyanwei@shinetechchina.com>

# Get started

composer require magtiny/api-manager dev-master

# Comment

Magtiny API manager runs depending on controller file's comments.

Before every controller class, you should add some comments like this:

	/**
	 * @magtiny		APIManager
	 * @controller	index
	**/
	class index {}

And before every controller action, you should add some comments like this:

	/**
	 * @magtiny		APIAction
	 * @action 		required
	 * @param 		getData.get				I am get param
	 * @param 		postData				I am post param
	 * @param 		requriedGetData.get.* 	I am required get param
	 * @param 		requriedPostData.*		I am required post param
	 * @param 		jsonPostData.json 		{"data": "I am post json data"}
	 * @method 		post
	**/
	public function required () {}

To start api manager, you should use the following code:

	use magtiny\tool\apiManager;
	$config = [
		"secret" => "xxxxxx",
		"instanceDir" => "controller dir name",
		"instanceUrl" => "api base url name",
	];
	$apiManager = new apiManager($config);
	return $apiManager->start();

# License

magtiny-framework is MIT licensed.