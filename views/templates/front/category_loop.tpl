
<div itemtype="#" itemscope="" class="sdsarticleCat clearfix">
    <div id="smartblogpost-{$post.id_post|escape:'htmlall':'UTF-8'}" class="smart-blog-post-single-item">
        <div class="sdsarticleHeader smart-blog-posts-header-area">
            <p class='title_block smart-blog-posts-title'>
              <a title="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                    href="{*{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}*}">
                {$post.meta_title|escape:'htmlall':'UTF-8'}</a>
            </p>

          {* <span class="smart-blog-posts-info">{if $smartshowauthor ==1}{l s='Posted by' mod='smartblog'}
               <span itemprop="author">&nbsp;<i class="icon icon-user"></i>&nbsp; {if $smartshowauthorstyle != 0}{$post.firstname|escape:'htmlall':'UTF-8'}
                   {$post.lastname|escape:'htmlall':'UTF-8'}{else}{$post.lastname|escape:'htmlall':'UTF-8'}
                   {$post.firstname|escape:'htmlall':'UTF-8'}{/if}
               </span> {/if}*}
{*                {$assocCats = BlogCategory::getPostCategoriesFull($post.id_post)}
                {$catCounts = 0}
                {if !empty($assocCats)}
                    &nbsp;&nbsp;<i class="icon icon-tags"></i>&nbsp;
                    <span itemprop="articleSection">
                        {foreach $assocCats as $catid=>$assoCat}
                            {if $catCounts > 0}, {/if}
                            {$catlink=[]}
                            {$catlink.id_category = $assoCat.id_category}
                            {$catlink.slug = $assoCat.link_rewrite}
                            <a
                                href="{$smartbloglink->getSmartBlogCategoryLink($assoCat.id_category,$assoCat.link_rewrite)|escape:'htmlall':'UTF-8'}">
                                {$assoCat.name|escape:'htmlall':'UTF-8'}
                            </a>
                            {$catCounts = $catCounts + 1}
                        {/foreach}
                    </span>
                {/if}*}
   {*             {if Configuration::get('smartenablecomment') == 1}
                &nbsp; <span class="comment">&nbsp;<i class="icon icon-comments"></i>&nbsp;
                    <a href="*}{*{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}*}{*#articleComments"
                        title="{$post.totalcomment|escape:'htmlall':'UTF-8'} Comments">{l s=' Comments'
        mod='smartblog'} <span class="comment-count-number">{$post.totalcomment}</span></a></span>{if $smartshowviewed ==1}&nbsp; <span class="smart-bg-views"><i
                        class="icon icon-eye-open"></i>{l s=' views' mod='smartblog'}
                <span class="comment-count-number">{$post.viewed|intval}</span>{/if}</span></span>
                {/if}*}
        </div>



        <div class="articleContent">
            {if isset($ispost) && !empty($ispost)}
                <a itemprop="url"
                    href="{*{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.cat_link_rewrite)|escape:'htmlall':'UTF-8'}*}"
                    title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">

                {/if}
                {*{assign var="img_link" value=$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}
                {if $img_link != 'false'}
                    <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                        src="{$img_link}"
                        class="imageFeatured">
                {/if}*}

                {if isset($ispost) && !empty($ispost)}
                </a>
            {/if}
            <div class="sdsarticle-des smart-blog-posts-short-description" style="text-align: left;">
{*                {$post.short_description}*}
            </div>
        </div>

        <div class="sdsreadMore smart-blog-post-readmore-btn">
            <a title="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                href="{*{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}*}"
                class="r_more btn btn-default button button-small"><span>{l s='Read More' mod='smartblog'}<i
                        class="icon-chevron-right right"></i></span></a>
        </div>
    </div>
</div>
