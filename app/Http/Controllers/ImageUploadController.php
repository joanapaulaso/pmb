<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    /**
     * Handle image upload from Quill editor
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        Log::info('Iniciando upload de imagem');

        try {
            // Validação do arquivo
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
            ]);

            Log::info('Validação do arquivo passou');

            // Obter o arquivo
            $image = $request->file('image');
            if (!$image) {
                Log::error('Nenhuma imagem recebida no upload');
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma imagem recebida'
                ], 400);
            }

            $originalName = $image->getClientOriginalName();
            Log::info('Processando imagem: ' . $originalName);

            // Criar nome único para o arquivo
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            $filename = Str::slug($filename) . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Preparar caminho de armazenamento
            $path = 'uploads/images/' . date('Y/m');
            $fullPath = public_path($path);

            // Garantir que o diretório existe
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Diretório criado: ' . $fullPath);
            }

            // Mover a imagem para o diretório
            $image->move($fullPath, $filename);
            Log::info('Imagem movida para: ' . $fullPath . '/' . $filename);

            // URL pública para a imagem
            $url = asset($path . '/' . $filename);
            Log::info('URL da imagem: ' . $url);

            // Retornar a URL da imagem
            return response()->json([
                'success' => true,
                'url' => $url,
                'alt' => pathinfo($originalName, PATHINFO_FILENAME)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao fazer upload de imagem: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload da imagem: ' . $e->getMessage()
            ], 500);
        }
    }
}
