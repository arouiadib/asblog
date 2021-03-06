<?php

class AsBlogBlogArchiveModuleFrontController extends ModuleFrontController {


	public $ssl = false;

	public function init() 
	{
		parent::init();
	}

	public function initContent() {
		parent::initContent();
		$blogcomment    = new Blogcomment();
		$day            = Tools::getvalue( 'day' );
		$year           = Tools::getvalue( 'year' );
		$month          = Tools::getvalue( 'month' );
		$title_category = '';
		$posts_per_page = Configuration::get( 'smartpostperpage' );
		$limit_start    = 0;
		$limit          = $posts_per_page;
		if ( (bool) Tools::getValue( 'page' ) ) {
			$c           = (int) Tools::getValue( 'page' );
			$limit_start = $posts_per_page * ( $c - 1 );
		}
		$result     = SmartBlogPost::getArchiveResult( $month, $year, $day, $limit_start, $limit );
		$total      = count( $result );
		$totalpages = ceil( $total / $posts_per_page );
		$i  = 0;
		$to = array();

		if ( ! empty( $result ) ) {
			foreach ( $result as $item ) {
				$to[ $i ] = $blogcomment->getToltalComment( $item['id_post'] );
				$i++;
			}
			$j = 0;
			foreach ( $to as $item ) {
				if ( $item == '' ) {
					$result[ $j ]['totalcomment'] = 0;
				} else {
					$result[ $j ]['totalcomment'] = $item;
				}

				$j++;
			}
		}
		$protocol_link    = ( Configuration::get( 'PS_SSL_ENABLED' ) ) ? 'https://' : 'http://';
		$protocol_content = ( isset( $useSSL ) and $useSSL and Configuration::get( 'PS_SSL_ENABLED' ) ) ? 'https://' : 'http://';
		$smartbloglink = new SmartBlogLink( $protocol_link, $protocol_content );
		$month_name = '';
		if ( $month ) {
			$monthNum   = $month;
			$dateObj    = DateTime::createFromFormat( '!m', $monthNum );
			$month_name = $dateObj->format( 'F' );
		}
		$this->context->smarty->assign(
			array(
				'smartbloglink'        => $smartbloglink,
				'postcategory'         => $result,
				'year'                 => $year,
				'month'                => $month,
				'month_name'           => $month_name,
				'day'                  => $day,
				'title_category'       => $title_category,
				'smartshowauthorstyle' => Configuration::get( 'smartshowauthorstyle' ),
				'limit'                => isset( $limit ) ? $limit : 0,
				'limit_start'          => isset( $limit_start ) ? $limit_start : 0,
				'c'                    => isset( $c ) ? $c : 1,
				'total'                => $total,
				'smartshowviewed'      => Configuration::get( 'smartshowviewed' ),
				'smartcustomcss'       => Configuration::get( 'smartcustomcss' ),
				'smartshownoimg'       => Configuration::get( 'smartshownoimg' ),
				'smartshowauthor'      => Configuration::get( 'smartshowauthor' ),
				'post_per_page'        => $posts_per_page,
				'pagenums'             => $totalpages - 1,
				'smartblogliststyle'   => Configuration::get( 'smartblogliststyle' ),
				'totalpages'           => $totalpages,
			)
		);
		$theme = Configuration::get('smarttheme') ? Configuration::get('smarttheme') : 'default';
		$templatepath = $this->get_template_path("archivecategory.tpl", $theme);

		if ($templatepath != "outside") {
			$this->setTemplate("module:smartblog/views/templates/front/themes/" . $templatepath . "/archivecategory.tpl");
		} else {
			$this->setTemplate("module:smartblog/views/templates/front/archivecategory.tpl");
		}
	}

}
