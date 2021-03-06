<?php
namespace Admin\Controller;
// use Think\Controller;

/**
 * @ClassName: Admin\Controller$ArticleListController 
 * @Description: 文章列表控制器文件
 * @author Saki <ilulu4ever816@gmail.com>
 * @date 2014-12-12 上午9:35:32 
 */
class ArticleListController extends AdminBaseController{
	
	
	/**
	 * @todo:  文章列表显示页面
	 * @author Saki <ilulu4ever816@gmail.com>
	 * @date 2014-12-12 上午9:35:49 
	 * @version V1.0
	 */
	public function index(){
		$this->display();
	}
	
	/**
	 * @todo: 文章列表表格ajax数据加载方法
	 * @author Saki <ilulu4ever816@gmail.com>
	 * @date 2014-12-12 上午10:21:33 
	 * @version V1.0
	 */
	public function jsondb(){
		$draw = $_GET['draw'];
		$start = $_GET['start'];
		$length = $_GET['length'];
		/*列表分页查询*/
		$model = new \Admin\Model\ArticleListModel();
		$list = $model->getArticleList_Page($start,$length);
		/*查询总条数*/
		$count = D('Admin/ArticleList')->count();
		/*生成JSON数据,安装前段表格框架的形式进行数据返回，jquery.datatable.js*/
		$result['draw'] = $draw;
		$result['recordsTotal'] = $count;
		$result['recordsFiltered'] = $count;
		$result['data'] = $list;
		echo json_encode($result);
	}
	
	/**
	 * @todo: 创建文章
	 * @author Saki <ilulu4ever816@gmail.com>
	 * @date 2014-12-15 下午3:56:05 
	 * @version V1.0
	 */
	public function create(){
		//类型列表
		$type_list = D('Admin/ArticleType')->where('status=1')->order('ctm desc')->select ();
		if(isset($_POST['Article'])){
			$model = new \Admin\Model\ArticleListModel();
			$admin_info = $this->admin_info;
			$data = $model->createArticle($_POST['Article'],$admin_info);
			echo json_encode($data);
		}else{
			$this->assign('type_list',$type_list);
			$this->assign('action','create');
			$this->display('form');
		}
	}
	
	/**
	 * @todo: 编辑文章
	 * @author Saki <ilulu4ever816@gmail.com>
	 * @date 2014-12-15 下午3:56:16 
	 * @version V1.0
	 */
	public function update(){
		//类型列表
		$type_list = D('Admin/ArticleType')->where('status=1')->order('ctm desc')->select ();
		$condition['id'] = $id = $_GET['id'];
		$model = new \Admin\Model\ArticleListModel();
		if(isset($_POST['Article'])){
			$data = $model->updateArticle($_POST['Article'], $id);
			echo json_encode($data);
		}else{
			$article_info = $model->where($condition)->find();
			$this->assign('article_info',$article_info);
			$this->assign('type_list',$type_list);
			$this->assign('action','update');
			$this->display('form');
		}
	}
	
	/**
	 * @todo: 删除文章
	 * @author Saki <ilulu4ever816@gmail.com>
	 * @date 2014-12-15 下午6:08:30 
	 * @version V1.0
	 */
	public function delete(){
		$id = $_GET['id'];
		$model = new \Admin\Model\ArticleListModel();
		$data = $model->deleteArticle($id);
		if($data['errcode'] == 0){
			$this->redirect('ArticleList/index');
		}
	}
	
	/**
	 * @todo: 预览文章
	 * @author Saki <ilulu4ever816@gmail.com>
	 * @date 2014-12-16 上午9:21:57 
	 * @version V1.0
	 */
	public function view(){
		$p = isset($_GET['p']) ? $_GET['p'] : 0;//评论分页标志
		//文章详细信息
		$model = new \Admin\Model\ArticleListModel();
		$info = $model->getArticleInfo($_GET['id']);
		$tags = explode(",",$info['tags']);
		//查询评论列表
		$comments_model = new \Admin\Model\ArticleCommentModel();
		//统计总条数
		$condition['aid'] = $_GET['id'];
		$condition['pid'] = 0;
		$count = $comments_model->where($condition)->count();
		//分页显示设置
		$Page = new \Think\Page($count,3);
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('theme','%FIRST%  %LINK_PAGE%  %END%');
		$show = $Page->show();
		$comments_list = $comments_model->getComments_First($Page,$condition);
		//输出
		$this->assign('info',$info);
		$this->assign('tags',$tags);
		$this->assign('comments_list',$comments_list);
		$this->assign('page',$show);
		$this->assign('p',$p);
		$this->display();
	}
	
}