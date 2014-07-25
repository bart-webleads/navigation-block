{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>
		{$lblNavigationBlock|ucfirst}
	</h2>
	<div class="buttonHolderRight">
        {option:hasCategories}
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAddNavigationBlock|ucfirst}">
			<span>{$lblAddNavigationBlock|ucfirst}</span>
		</a>
        {/option:hasCategories}
	</div>
</div>

{option:dataGrid}
	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}

{option:!hasCategories}
    <h3>{$msgNeedToCreateCategorieFirst}</h3>
    <p>{$msgNoCategories}</p>
{/option:!hasCategories}

{option:!dataGrid}
	<p>{$msgNoItems}</p>
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
