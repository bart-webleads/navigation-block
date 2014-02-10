{*
	variables that are available:
	{widgetNavigationBlockDetail}
*}

{option:widgetNavigationBlockDetail.items}
    <section class="widgetNavigationBlock mod {option:widgetNavigationBlockDetail.category.url}{$widgetNavigationBlockDetail.category.url}{/option:widgetNavigationBlockDetail.category.url}">
        <div class="inner">
            <label>{option:widgetNavigationBlockDetail.category.title}{$widgetNavigationBlockDetail.category.title}{/option:widgetNavigationBlockDetail.category.title}</label>
            <ul>
                {iteration:widgetNavigationBlockDetail.items}
                    <li class="{option:widgetNavigationBlockDetail.items.class}{$widgetNavigationBlockDetail.items.class}{/option:widgetNavigationBlockDetail.items.class}{option:widgetNavigationBlockDetail.items.selected} selected{/option:widgetNavigationBlockDetail.items.selected}">
                        <a title="{option:!widgetNavigationBlockDetail.items.description}{$widgetNavigationBlockDetail.items.title}{/option:!widgetNavigationBlockDetail.items.description}{option:widgetNavigationBlockDetail.items.description}{$widgetNavigationBlockDetail.items.description}{/option:widgetNavigationBlockDetail.items.description}"
                           href="{$widgetNavigationBlockDetail.items.full_url}">{$widgetNavigationBlockDetail.items.title}</a>
                        {option:widgetNavigationBlockDetail.items.childrenHtml}{$widgetNavigationBlockDetail.items.childrenHtml}{/option:widgetNavigationBlockDetail.items.childrenHtml}
                    </li>
                {/iteration:widgetNavigationBlockDetail.items}
            </ul>
        </div>
    </section>
{/option:widgetNavigationBlockDetail.items}
