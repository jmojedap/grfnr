<?php
    $options_parent = array();
?>    
        
<form accept-charset="utf-8" id="itemForm" @submit.prevent="handleSubmit">
    <fieldset v-bind:disabled="loading">
        <input id="field-categoria_id" name="category_id" type="hidden" v-model="currCategory.cod">

        <div class="mb-2 row">
            <div class="col-sm-8 offset-4">
                <button class="btn w120p" v-bind:class="appState.buttonClass" type="submit">
                    {{ appState.buttonText }}
                </button>
                <button type="button" class="btn btn-light w120p" v-on:click="autocomplete" title="Completar automáticamente los campos secundarios faltantes">
                    <i class="fa fa-magic"></i>
                    Completar
                </button>
            </div>
        </div>

        <div class="mb-2 row">
            <label for="cod" class="col-md-4 col-form-label text-right">
                <span class="float-rigth">Código</span>
            </label>
            <div class="col-md-8">
                <input
                    name="cod" ref="field_cod" class="form-control" placeholder="Código numérico"
                    title="Código numérico" required
                    v-model="fields.cod"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="item_name" class="col-md-4 col-form-label text-right">
                <span class="float-rigth">Nombre</span>
            </label>
            <div class="col-md-8">
                <input
                    name="item_name" ref="field_item_name" class="form-control" title="Nombre del ítem"
                    required
                    v-model="fields.item_name" v-on:change="autocomplete"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="description" class="col-md-4 col-form-label text-right">Descripción</label>
            <div class="col-md-8">
                <textarea
                    name="description"
                    class="form-control"
                    placeholder=""
                    title="Descripción del ítem"
                    required
                    v-model="fields.description"
                    rows="3"
                    ></textarea>
            </div>
        </div>

        <div class="mb-2 row">
            <label for="item_group" class="col-md-4 col-form-label text-right">Grupo</label>
            <div class="col-md-8">
                <input
                    name="item_group" class="form-control" title="Grupo item"
                    v-model="fields.item_group"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="filters" class="col-md-4 col-form-label text-right">Filtros</label>
            <div class="col-md-8">
                <input
                    id="field-filtro"
                    name="filters"
                    class="form-control"
                    placeholder=""
                    title="Filtros del ítem"
                    v-model="fields.filters"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="parent_id" class="col-sm-4 col-form-label text-right">Padre</label>
            <div class="col-sm-8">
                <select name="parent_id" id="field-parent_id" v-model="fields.parent_id" class="form-control">
                    <option value="">[ Ninguno ]</option>
                    <option v-for="(item, item_key) in items" v-bind:value="`0` + item.cod" v-show="item.id != rowId">
                        {{ item.item_name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="mb-2 row">
            <label for="slug" class="col-md-4 col-form-label text-right">
                <span class="float-rigth">Slug</span>
            </label>
            <div class="col-md-8">
                <input name="slug" class="form-control" title="Sin espacios y acentos"
                    required
                    v-model="fields.slug"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="abbreviation" class="col-md-4 col-form-label text-right">Abreviatura</label>
            <div class="col-md-8">
                <input name="abbreviation" class="form-control" title="Abreviatura de hasta 4 caracteres"
                    v-model="fields.abbreviation"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="long_name" class="col-md-4 col-form-label text-right">
                <span class="float-rigth">Nombre largo</span>
            </label>
            <div class="col-md-8">
                <input name="long_name" class="form-control" required
                    v-model="fields.long_name"
                    >
            </div>
        </div>

        <div class="mb-2 row">
            <label for="short_name" class="col-md-4 col-form-label text-right">
                <span class="float-rigth">Nombre corto</span>
            </label>
            <div class="col-md-8">
                <input name="short_name" class="form-control" required maxlength="25"
                    v-model="fields.short_name"
                    >
            </div>
        </div>
        <div class="mb-2 row">
            <label for="color" class="col-md-4 col-form-label text-right">
                <span class="float-rigth">Color</span>
            </label>
            <div class="col-md-8">
                <input
                    name="color" class="form-control"
                    v-model="fields.color"
                    >
            </div>
        </div>
    </fieldset>
</form>