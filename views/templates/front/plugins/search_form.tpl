<div id="blog-search" class="block">
    <h2 class="block_title">
        {l s='Search in Blog' d='Modules.Asblog.Shop'}
    </h2>
    <div class="blog-search-form">
        <form class=""
              method="post"
              action="{$bloglink->getBlogLink('module-asblog-blogsearch')|escape:'htmlall':'UTF-8'}">
            <div>
                <span class="search_input">
                    <input type="text"
                           class="form-control"
                           value="{*{$search|escape:'htmlall':'UTF-8'}*}"
                           name="search"
                           id="search_query">
                </span>
                <span class="search_button">
                    <button class="btn btn-default button button-small"
                              value="{l s='Ok'  d='Modules.Asblog.Shop'}"
                              name="searchblogsubmit"
                              type="submit">
                        <i class="material-icons">search</i>
                    </button>
                </span>
            </div>
        </form>
    </div>
</div>