<div class="input-group float-right" style="width: 120px;">
    <button class="btn btn-light" v-on:click="sumPage(-1)" title="Página anterior">
        <i class="fa fa-caret-left"></i>
    </button>
    <input
        name="numPage" type="number" class="form-control"
        value="1" min="1"
        v-bind:max="maxPage"
        v-on:change="getList"
        v-bind:title="`${maxPage} páginas en total`"
        >
    <button class="btn btn-light" v-on:click="sumPage(1)" title="Página siguiente">
        <i class="fa fa-caret-right"></i>
    </button>
</div>