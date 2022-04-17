<div class="blog-search">
    <h1>{l s='Search in Blog' d='Modules.Asblog.Shop'}</h1>
    <div class="blog-search-form">
        <form class=""
              method="post"
              action="{$bloglink->getBlogLink('module-asblog-search_rule')|escape:'htmlall':'UTF-8'}">
            <fieldset>
                <div class="">
                    <input type="text"
                           class="form-control"
                           value="{*{$search|escape:'htmlall':'UTF-8'}*}"
                           name="search"
                           id="search_query">
                    <button class="btn btn-default button button-small"
                            value="{l s='Ok'  d='Modules.Asblog.Shop'}"
                            name="searchblogsubmit"
                            type="submit">
                        <span>{l s='Ok' d='Modules.Asblog.Shop'}</span>
                    </button>
                </div>
            </fieldset>
        </form>
    </div>
</div>