<?php

use Mitsuki\Database\Schema\Schema;
use Mitsuki\Database\Table;
use Mitsuki\Database\Migrations\Migration;
use Phinx\Db\Table as PhinxTable;

/**
 * Unit tests for the Mitsuki Migration Schema Builder.
 * * These tests verify that the fluent API correctly translates method calls 
 * into the appropriate Phinx instructions using Mockery to intercept calls.
 */
beforeEach(function () {
    /** @var PhinxTable|\Mockery\MockInterface */
    $this->phinxTableMock = Mockery::mock(PhinxTable::class);
});

test('string method creates a column with correct options', function () {
    // Assert table does not exist, so it should trigger create()
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(false);

    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('username', 'string', Mockery::on(function ($options) {
            return $options['limit'] === 100 && $options['null'] === true;
        }));

    $this->phinxTableMock->shouldReceive('create')->once();

    $table = new Table($this->phinxTableMock);
    $table->string('username', 100)->nullable();
    $table->save();
});

test('decimal method handles precision and scale', function () {
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(false);

    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('price', 'decimal', Mockery::on(function ($options) {
            return $options['precision'] === 10 && $options['scale'] === 2;
        }));

    $this->phinxTableMock->shouldReceive('create')->once();

    $table = new Table($this->phinxTableMock);
    $table->decimal('price', 10, 2);
    $table->save();
});

test('foreignIdFor method generates correct column name and constraints', function () {
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(false);

    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('user_id', 'integer', Mockery::any());

    $this->phinxTableMock->shouldReceive('addForeignKey')
        ->once()
        ->with('user_id', 'users', 'id', Mockery::on(function ($options) {
            return $options['delete'] === 'CASCADE' && $options['update'] === 'RESTRICT';
        }));

    $this->phinxTableMock->shouldReceive('create')->once();

    $table = new Table($this->phinxTableMock);

    $table->foreignIdFor('App\Models\User')
        ->constrained()
        ->onDelete('cascade')
        ->onUpdate('restrict');

    $table->save();
});

test('it updates the table if it already exists', function () {
    // Simulate that the table already exists in the database
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(true);

    $this->phinxTableMock->shouldReceive('addColumn')->once()->with('bio', 'text', Mockery::any());

    // Should call update() instead of create()
    $this->phinxTableMock->shouldReceive('update')->once();
    $this->phinxTableMock->shouldNotReceive('create');

    $table = new Table($this->phinxTableMock);
    $table->text('bio');
    $table->save();
});

test('schema runner executes the callback correctly', function () {
    /** @var Migration|\Mockery\MockInterface */
    $migrationMock = Mockery::mock(Migration::class);

    $migrationMock->shouldReceive('table')
        ->with('posts')
        ->andReturn($this->phinxTableMock);

    // The internal save() will call exists()
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(false);
    $this->phinxTableMock->shouldReceive('addColumn')->atLeast()->once();
    $this->phinxTableMock->shouldReceive('create')->once();

    Schema::create('posts', function (Table $table) {
        $table->string('title');
    }, $migrationMock);
});

test('it updates an existing table instead of creating a new one', function () {
    // We expect the 'exists' check to return true
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(true);

    // We expect addColumn to be called for the new column
    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('bio', 'text', Mockery::any());

    // IMPORTANT: For an existing table, Phinx uses update() instead of create()
    $this->phinxTableMock->shouldReceive('update')->once();
    $this->phinxTableMock->shouldNotReceive('create');

    $table = new Table($this->phinxTableMock);
    $table->text('bio');

    // We need to logic in our save() method to handle update vs create
    $table->save();
});

test('it adds a new column to an existing table without duplication', function () {
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(true);

    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('new_feature_flag', 'boolean', Mockery::any());

    $this->phinxTableMock->shouldReceive('update')->once();

    $table = new Table($this->phinxTableMock);
    $table->boolean('new_feature_flag');
    $table->save();
});

test('id method creates an auto-incrementing primary key', function () {
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(false);

    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('id', 'integer', Mockery::on(function ($options) {
            // On vérifie que l'option identity est bien présente pour l'auto-incrément
            return $options['identity'] === true;
        }));

    $this->phinxTableMock->shouldReceive('create')->once();

    $table = new Table($this->phinxTableMock);
    $table->id();
    $table->save();
});

test('timestamps method creates created_at and updated_at columns', function () {
    $this->phinxTableMock->shouldReceive('exists')->once()->andReturn(false);

    // On s'attend à deux appels à addColumn
    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('created_at', 'timestamp', Mockery::on(function ($options) {
            return $options['default'] === 'CURRENT_TIMESTAMP';
        }));

    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('updated_at', 'timestamp', Mockery::on(function ($options) {
            return $options['default'] === 'CURRENT_TIMESTAMP' 
                && $options['update'] === 'CURRENT_TIMESTAMP';
        }));

    $this->phinxTableMock->shouldReceive('create')->once();

    $table = new Table($this->phinxTableMock);
    $table->timestamps();
    $table->save();
});