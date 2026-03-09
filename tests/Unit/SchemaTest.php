<?php 

use Mitsuki\Database\Schema\Schema;
use Mitsuki\Database\Table;
use Mitsuki\Migrations\Migration;
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
    // Assert that addColumn is called with proper type and constraints
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
    // Assert that numeric precision and scale are correctly passed to the options array
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
    // 1. Verify the foreign key column is created as an integer
    $this->phinxTableMock->shouldReceive('addColumn')
        ->once()
        ->with('user_id', 'integer', Mockery::any());

    // 2. Verify the foreign key constraint is attached with cascading rules
    $this->phinxTableMock->shouldReceive('addForeignKey')
        ->once()
        ->with('user_id', 'users', 'id', Mockery::on(function ($options) {
            return $options['delete'] === 'CASCADE' && $options['update'] === 'RESTRICT';
        }));

    $this->phinxTableMock->shouldReceive('create')->once();

    $table = new Table($this->phinxTableMock);

    // Act: define relationship based on a Model class string
    $table->foreignIdFor('App\Models\User')
          ->constrained()
          ->onDelete('cascade')
          ->onUpdate('restrict');
          
    $table->save();
});

test('schema runner executes the callback correctly', function () {
    /** @var Migration|\Mockery\MockInterface */
    $migrationMock = Mockery::mock(Migration::class);
    
    // Ensure the migration provides the table instance to the schema builder
    $migrationMock->shouldReceive('table')
        ->with('posts')
        ->andReturn($this->phinxTableMock);
    
    $this->phinxTableMock->shouldReceive('addColumn')->atLeast()->once();
    $this->phinxTableMock->shouldReceive('create')->once();

    // Verify the static Schema::create interface works as expected
    Schema::create('posts', function (Table $table) {
        $table->string('title');
    }, $migrationMock);
});