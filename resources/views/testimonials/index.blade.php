@extends('layouts.app')

@section('content')
    @php
        $isAppartPage = request()->is('setting/index/appart');
        $isPropertyPage = request()->is('setting/index/property');
    @endphp

    <div class="main-content-inn">

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="bi bi-chat-right me-2 text-danger"></i>Témoignages
                    </h3>
                    
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <!-- Breadcrumb -->

            <!-- Header avec bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <small class="mb-0">Gestion des témoignages</small>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Témoignage
                </button>
            </div>

            <!-- Tableau -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des témoignages</h5>
                    <span id="totalCount" class="badge bg-secondary">{{ $testimonials->count() }} Témoignage{{ $testimonials->count() > 1 ? 's' : '' }}</span>
                </div>
                <div class="table-responsive table-container p-3">
                    <table class="table table-striped align-middle mb-0" id="example2">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Code</th>
                                <th>Nom du client</th>
                                <th>Témoignage</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($testimonials as $item)
                               <tr>
                                   <td class="fw-semibold">#{{ $item->code ?? '' }}</td>
                                   <td>{{ $item->name ?? '' }}</td>
                                   <td class="text-wrap">{!! Str::words($item->content ?? '', 20, '...') !!}</td>
                                   <td>
                                       <div class="d-flex justify-content-between align-items-center">
                                           <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle" 
                                              data-bs-toggle="modal" data-bs-target="#showTestimonialModal{{ $item->uuid }}"
                                               title="Modifier">
                                           <i class="fas fa-eye"></i>
                                           </button>
                                           <button class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" 
                                              data-bs-toggle="modal" data-bs-target="#editTestimonialModal{{ $item->uuid }}"
                                               title="Modifier">
                                           <i class="fas fa-edit"></i>
                                           </button>
                                           <button class="btn btn-sm btn-icon btn-outline-danger rounded-circle">
                                            <a class="deleteConfirmation" data-uuid="{{$item->uuid}}"
                                            data-type="confirmation_redirect" data-placement="top"
                                            data-token="{{ csrf_token() }}"
                                            data-url="{{route('admin.destroy.testimonial',$item->uuid)}}"
                                            data-title="Vous êtes sur le point de supprimer le témoignage de {{$item->name}} "
                                            data-id="{{$item->uuid}}" data-param="0"
                                            data-route="{{route('admin.destroy.testimonial',$item->uuid)}}"><i
                                                class='fas fa-trash' style="cursor: pointer"></i></a>
                                           </button>
                                       </div>
                                   </td>
                               </tr>

                               @include('admins.components.testimonialModal.edit')
                               @include('admins.components.testimonialModal.show')
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

    @include('admins.components.testimonialModal.add')

@endsection