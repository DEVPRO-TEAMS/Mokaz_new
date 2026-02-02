@extends('layouts.app')

@section('content')
    @php
        $isAppartPage = request()->is('setting/index/appart');
        $isPropertyPage = request()->is('setting/index/property');
        $isCategoryPage = request()->is('setting/index/category');
    @endphp

    <div class="main-content-inn">

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-handshake me-2 text-danger"></i> Reglages
                    </h3>
                    
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <!-- Breadcrumb -->

            <!-- Header avec bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <small class="mb-0">Gestion des Variables</small>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVariableModal">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Variable
                </button>
            </div>

            <!-- Tableau -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des variables</h5>
                    <span id="totalCount" class="badge bg-secondary">{{ $variables->count() }} variables</span>
                </div>
                <div class="table-responsive table-container p-3">
                    <table class="table table-striped align-middle mb-0" id="example2">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Code</th>
                                <th>Libellé</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>État</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($variables as $item)
                               <tr>
                                   <td class="fw-semibold">#{{ $item->code ?? '' }}</td>
                                   <td>{{ $item->libelle ?? '' }}</td>
                                   <td>{{ $item->description ?? '' }}</td>
                                   <td>

                                        @if ($item->type == 'commodity')
                                            <span class="badge bg-primary text-light">Commodité</span>
                                        @elseif ($item->type == 'type_of_property')
                                            <span class="badge bg-warning text-light">Type de bien</span>
                                        @elseif ($item->type == 'type_of_appart')
                                            <span class="badge bg-success text-light">Type d'appart</span>
                                        @elseif ($item->type == 'category_of_property')
                                            <span class="badge bg-primary text-light">Categorie de bien</span>
                                        @elseif ($item->type == 'autre')
                                            <span>Autre</span>
                                        @endif

                                   </td>
                                   <td>
                                    @if ($item->etat == 'actif')
                                        <span class="badge bg-success  text-light">
                                           <i class="fas fa-check me-1"></i>
                                           Actif
                                       </span>
                                    @else
                                        <span class="badge bg-danger text-light">
                                           <i class="fas fa-check me-1"></i>
                                           Inactif
                                       </span>
                                    @endif
                                   </td>
                                   <td>
                                       <div class="d-flex justify-content-between align-items-center">
                                           <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle" 
                                              data-bs-toggle="modal" data-bs-target="#editVariableModal{{ $item->uuid }}"
                                               title="Modifier">
                                           <i class="fas fa-edit"></i>
                                           </button>
                                           <button class="btn btn-sm btn-icon btn-outline-danger rounded-circle">
                                            <a class="deleteConfirmation" data-uuid="{{$item->uuid}}"
                                            data-type="confirmation_redirect" data-placement="top"
                                            data-token="{{ csrf_token() }}"
                                            data-url="{{route('setting.destroyVariable',$item->uuid)}}"
                                            data-title="Vous êtes sur le point de supprimer {{$item->libelle}} "
                                            data-id="{{$item->uuid}}" data-param="0"
                                            data-route="{{route('setting.destroyVariable',$item->uuid)}}"><i
                                                class='fas fa-trash' style="cursor: pointer"></i></a>
                                           </button>
                                       </div>
                                   </td>
                               </tr>

                               @include('admins.components.editVariableModal')
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Pagination" class="mt-4">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- Pagination sera générée ici -->
                </ul>
            </nav>
        </div>
    </div>

    @include('admins.components.addVariableModal')

@endsection