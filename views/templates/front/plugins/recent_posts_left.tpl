<div id="blog-recent-posts-left" class="block">
    {if isset($posts) AND !empty($posts)}
        <div id="recent-articles-block-left"  class="block">
            <h2 class="block_title">
                    {l s='Recent Articles' d='Modules.Asblog.Shop'}
            </h2>
            <div class="block_content">
                <ul class="posts-list">
                    {foreach from=$posts item="post"}
                        <li>
                            {assign var="image_url" value="`$urls.img_ps_url`blog/post/`$post.post_img`.jpeg"}
                            {if $post.post_img === 'no'}
                                {$image_url = "`$urls.img_ps_url`blog/no.jpg"}
                            {/if}
                            <a class="image" title="{$post.meta_title}"
                               href="{$bloglink->getBlogPostLink($post.id_post, $post.link_rewrite)|escape:'htmlall':'UTF-8'}">
                                <div class="article-image">
                                    <img alt="{$post.meta_title}" src="{$image_url}">
                                </div>
                                <div class="article-title-and-date">
                                    <h3 class="article-title">{$post.meta_title}</h3>
                                    <div class="article-date">{$post.date_add|date_format:"%b %d, %Y"}</div>
                                </div>
                                <div class="clearfix"></div>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {/if}
</div>

