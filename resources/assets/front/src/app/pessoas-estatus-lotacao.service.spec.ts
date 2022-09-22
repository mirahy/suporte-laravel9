import { TestBed } from '@angular/core/testing';

import { PessoasEstatusLotacaoService } from './pessoas-estatus-lotacao.service';

describe('PessoasEstatusLotacaoService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: PessoasEstatusLotacaoService = TestBed.get(PessoasEstatusLotacaoService);
    expect(service).toBeTruthy();
  });
});
