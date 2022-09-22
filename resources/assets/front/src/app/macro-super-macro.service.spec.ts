import { TestBed } from '@angular/core/testing';

import { MacroSuperMacroService } from './macro-super-macro.service';

describe('MacroSuperMacroService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: MacroSuperMacroService = TestBed.get(MacroSuperMacroService);
    expect(service).toBeTruthy();
  });
});
