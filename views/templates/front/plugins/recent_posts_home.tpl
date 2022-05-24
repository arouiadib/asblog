<section id="blog-recent-posts-home" class="block">

    {if isset($posts) AND !empty($posts)}
        <div id="recent-articles-block-home"  class="block">
            <h2 class="block_title h2 products-section-title text-uppercase">
                {l s='Recent Articles' d='Modules.Asblog.Recent_posts_home'}
            </h2>

            <div class="block_content container-blog-home container">
                <div class="row">
                    {foreach from=$posts item="post"}
                        <div class="card col-sm-6 col-md-4 pl-0 pr-0 ml-0 mr-1">
                            {assign var="image_url" value="`$urls.img_ps_url`blog/post/`$post.post_img`.jpeg"}
                            {if $post.post_img === 'no'}
                                {$image_url = "`$urls.img_ps_url`blog/no.jpg"}
                            {/if}

                            <a class="image" title="{$post.meta_title}"
                               href="{$bloglink->getBlogPostLink($post.id_post, $post.link_rewrite)|escape:'htmlall':'UTF-8'}">
                                <div class="card__header">
                                    <img src="{$image_url}" alt="{$post.meta_title}" class="card__image" width="600">
                                </div>
                                <div class="card__body">
                                    <span class="tag tag-brown">{$post.category}</span>
                                    <h4>{$post.title}</h4>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi perferendis molestiae non nemo doloribus. Doloremque, nihil! At ea atque quidem!</p>
                                </div>
                                <div class="card__footer">
                                    <div class="user">
                                        <img src="{$post.author_image}" alt="user__image" class="user__image" width="40">
                                        <div class="user__info">
                                            <h5>{$post.firstname} {$post.lastname}</h5>
                                            <small>{$post.date_add|date_format:"%b %d, %Y"}</small>
                                        </div>
                                    </div>
                                </div>
                                {*<div class="clearfix"></div>*}
                            </a>
                        </div>
                    {/foreach}
                </>
            </div>

        </div>
        <div class="block_button">
            <a href="{$bloglink->getBlogLink('module-asblog-bloglist')}" class="btn btn-primary btn-xxl page-scroll">
                {l s='All posts' d='Modules.Asblog.Shop'}
            </a>
        </div>
    {/if}
</section>
