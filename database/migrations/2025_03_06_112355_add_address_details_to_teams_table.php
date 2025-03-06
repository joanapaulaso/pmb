<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Campos básicos de localização física
            $table->string('building')->nullable()->comment('Nome do prédio ou bloco');
            $table->string('floor')->nullable()->comment('Andar');
            $table->string('room')->nullable()->comment('Número da sala');

            // Informações de contato
            $table->string('phone')->nullable()->comment('Telefone do laboratório');
            $table->string('contact_email')->nullable()->comment('Email de contato do laboratório');
            $table->string('contact_person')->nullable()->comment('Nome da pessoa de contato');

            // Informações complementares
            $table->string('complement')->nullable()->comment('Complemento do endereço');
            $table->string('reference_point')->nullable()->comment('Pontos de referência próximos');
            $table->string('postal_code')->nullable()->comment('CEP/Código postal');

            // Informações operacionais
            $table->string('working_hours')->nullable()->comment('Horário de funcionamento');
            $table->string('website')->nullable()->comment('Site ou página web');
            $table->boolean('has_accessibility')->default(false)->comment('Indicação de acessibilidade');

            // Campo para observações
            $table->text('address_notes')->nullable()->comment('Observações sobre o endereço');

            // Campos adicionais que podem ser úteis
            $table->string('department')->nullable()->comment('Departamento ou setor');
            $table->string('campus')->nullable()->comment('Campus (para universidades)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'building',
                'floor',
                'room',
                'phone',
                'contact_email',
                'contact_person',
                'complement',
                'reference_point',
                'postal_code',
                'working_hours',
                'website',
                'has_accessibility',
                'address_notes',
                'department',
                'campus'
            ]);
        });
    }
};
