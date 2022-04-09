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
